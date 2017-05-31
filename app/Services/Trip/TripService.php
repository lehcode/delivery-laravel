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
use Illuminate\Support\Facades\DB;

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
	 * @param integer $id
	 *
	 * @return Trip
	 */
	public function item($id)
	{
		return $this->tripRepository->find($id);
	}


}
