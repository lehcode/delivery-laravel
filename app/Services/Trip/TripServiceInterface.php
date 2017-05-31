<?php

/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 20:20
 */

namespace App\Services\Trip;

use App\Models\Trip;
use Jenssegers\Date\Date;

/**
 * Interface TripServiceInterface
 * @package App\Services\Trip
 */
interface TripServiceInterface
{
	/**
	 * @param array $tripData
	 * @param array $tripDetails
	 * @return Trip
	 */
	public function create(array $tripData, array $tripDetails);

	/**
	 * @param Trip $trip
	 * @param array $params
	 * @return Trip
	 */
	public function edit(Trip $trip, array $params);

	/**
	 * @return Builder
	 */
	public function all();

	/**
	 * @param $trip_id
	 *
	 * @return mixed
	 */
	public function item($trip_id);
}
