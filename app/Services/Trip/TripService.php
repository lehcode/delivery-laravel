<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 16:54
 */

namespace App\Services\Trip;

use App\Models\Trip;
use App\Repositories\Trip\TripRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
	 * @param TripRepositoryInterface $tripRepositoryInterface
	 */
	public function __construct(TripRepositoryInterface $tripRepositoryInterface)
	{
		$this->tripRepository = $tripRepositoryInterface;
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
	 * @param array $tripDetails
	 *
	 * @return mixed
	 */
	public function create(array $tripData, array $tripDetails)
	{
		return DB::transaction(function () use ($tripData, $tripDetails) {
			$trip = Trip::create($tripData);
			return $trip;
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all()
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
	 * @param $trip_id
	 */
	public function item($trip_id)
	{
		// TODO: Implement item() method.
	}


}
