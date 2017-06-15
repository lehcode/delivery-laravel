<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 16:54
 */

namespace App\Services\Trip;

use App\Exceptions\MultipleExceptions;
use App\Http\Responses\TripResponse;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\Trip\TripRepository;
use App\Repositories\Trip\TripRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BaseServiceInterface;
use Jenssegers\Date\Date;

/**
 * Class TripService
 * @package App\Services\Trip
 */
class TripService implements TripServiceInterface
{
	/**
	 * @var TripRepositoryInterface
	 */
	protected $tripRepository;

	/**
	 * TripService constructor.
	 *
	 * @param TripRepository $tripRepository
	 */
	public function __construct(TripRepository $tripRepository)
	{
		$this->tripRepository = $tripRepository;
	}

	/**
	 * @return mixed
	 */
	public function getList()
	{
		return $this->tripRepository->getBuilder()->enabled();
	}


	/**
	 * @param array $tripData
	 *
	 * @return mixed
	 * @throws MultipleExceptions
	 */
	public function create(array $tripData)
	{

		$now = Date::now();
		$dd = Date::createFromFormat('Y-m-d H:i:s', $tripData['departure_date']);

		if ($dd->lte($now)) {
			throw new MultipleExceptions("Trip departure date must be in the future", 400);
		}

		return DB::transaction(function () use ($tripData) {
			$trip = Trip::create($tripData);
			return $trip;
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all()
	{
		return $this->tripRepository->all();
	}

	/**
	 * @return mixed
	 */
	public function userTrips()
	{
		return $this->tripRepository->userTrips();
	}

	/**
	 * @param Trip  $trip
	 * @param array $params
	 */
	public function edit(Trip $trip, array $params)
	{
		// TODO: Implement edit() method.
	}

	/**
	 * @param $id
	 *
	 * @return $this|bool
	 * @throws MultipleExceptions
	 */
	public function item($id)
	{
		$item = $this->tripRepository->find($id);

		if (!$item) {
			throw new MultipleExceptions("Trip not found", 400);
		}

		return $item;
	}

	/**
	 * @return array
	 */
	public function getTripsFromCurrentCity()
	{

		$authUser = Auth::user();
		$role = $authUser->roles()->first()->name;

		switch ($role) {
			case User::ROLE_CUSTOMER:
				$user = $authUser->customer();
				$city = $user->first()
					->currentCity()->where('active', true)->first();
				break;
			case User::ROLE_CARRIER:
				$user = $authUser->carrier();
				$city = $user->first()
					->currentCity()->where('active', true)->first();
				break;
		}

		$result = $this->tripRepository->findByParams(['from_city_id' => $city->id])
			->map(function ($item) {
				$response = new TripResponse();
				return $response->transform($item);
			});

		return $result;
	}

	/**
	 * @param array $dates
	 *
	 * @return mixed
	 */
	public function getListByStartAndEnd(array $dates)
	{
		$trips = Trip::where('departure_date', '>=', $dates['start'])
			->where('departure_date', '<=', $dates['end'])
			->get()
			->map(function ($item) {

				$arr = $item->toArray();
				unset(
					$arr['carrier_id'],
					$arr['payment_type_id'],
					$arr['from_city_id'],
					$arr['to_city_id']
				);

				$arr['carrier'] = $item->carrier;
				$arr['payment_type'] = $item->paymentType;
				$arr['from_city'] = $item->fromCity;
				$arr['dest_city'] = $item->destinationCity;

				return $arr;
			});

		return $trips;
	}

	public function getListByDate($date)
	{
		$df = $date->format("Y-m-d");
		$trips = Trip::where('departure_date', 'LIKE', $df . '%')
			->get()
			->map(function ($item) {

				$arr = $item->toArray();
				unset(
					$arr['carrier_id'],
					$arr['payment_type_id'],
					$arr['from_city_id'],
					$arr['to_city_id']
				);

				$arr['carrier'] = $item->carrier;
				$arr['payment_type'] = $item->paymentType;
				$arr['from_city'] = $item->fromCity;
				$arr['dest_city'] = $item->destinationCity;

				return $arr;
			});

		return $trips;
	}

	/**
	 * @param int $cityId
	 */
	public function getCity($cityId)
	{
		return City::where('id', '=', $cityId)->get();
	}

}
