<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 16:54
 */

namespace App\Services\Trip;

use App\Http\Responses\TripResponse;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\Trip\TripRepository;
use App\Repositories\Trip\TripRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BaseServiceInterface;

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
	 */
	public function create(array $tripData)
	{



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
	 * @param int $id
	 *
	 * @return Trip
	 */
	public function item($id)
	{
		return $this->tripRepository->find($id);
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

}
