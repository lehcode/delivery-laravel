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
use App\Models\Country;
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
	 * @return \Illuminate\Support\Collection
	 * @throws MultipleExceptions
	 */
	public function getTripsFromCurrentCity()
	{


		if (Auth::user()->hasRole('customer')) {

			$city = Auth::user()->customer()->first()
				->currentCity()
				->where('active', true)->first();

			$result = $this->tripRepository->findByParams(['from_city_id' => $city->id])
				->map(function ($item) {
					$response = new TripResponse();
					return $response->transform($item);
				});

			return $result;
		}

		throw new MultipleExceptions("Access denied", 403);


	}

	/**
	 * @param $cityName
	 *
	 * @return Collection
	 */
	public function getTripsFromCity($cityName)
	{
		$city = City::where('name', '=', $cityName)->first();
		$trips = Trip::where('from_city_id', '=', $city->id)->get();

		return $trips;
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
			->join('users', 'users.id', '=', 'trips.carrier_id')
			->where('users.is_enabled', '=', true)
			->distinct()
			->orderBy('departure_date')
			->get();

		return $trips;
	}

	/**
	 * @param Date $date
	 *
	 * @return Collection
	 */
	public function getListByDate(Date $date)
	{
		$trips = Trip::where('departure_date', '>=', $date->format("Y-m-d"))
			->join('users', 'users.id', '=', 'trips.carrier_id')
			->where('users.is_enabled', '=', true)
			->distinct()
			->orderBy('departure_date')
			->get();

		return $trips;
	}

	/**
	 * @param int $cityId
	 *
	 * @return array
	 */
	public function getCity($cityId)
	{
		$result = City::with('country')
			->where('id', '=', (int)$cityId)
			->where('active', '=', true)
			->first();

		$clone = $result->toArray();
		$clone['country'] = $result->country->toArray();

		return $clone;
	}

	/**
	 * @return Collection|static[]
	 */
	public function getAllCities()
	{

		$result = Country::with('cities')->get();

		return $result;

	}

	/**
	 * @param string $search
	 * @param string $countryCode
	 *
	 * @return \Illuminate\Database\Query\Builder
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function findCity($search, $countryCode)
	{

		\Validator::make(['search' => $search, 'country_code' => $countryCode], [
			'search' => 'required|string|min:2|max:64',
			'country_code' => 'required|string|size:2|exists:countries,alpha2_code',
		])->validate();

		$country = Country::where('alpha2_code', $countryCode)->get()->first();

		$result = \DB::table('cities')
			->where('name', 'like', '%' . $search . '%')
			->where('country_id', '=', $country->id)
			->where('active', '=', true)
			->orderBy('name')
			->get()
			->each(function ($item) {
				$item->country_id = null;
				$item->active = null;
				return $item;
			});

		return $result;
	}

}
