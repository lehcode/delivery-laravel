<?php

/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 20:20
 */

namespace App\Services\Trip;

use Jenssegers\Date\Date;

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
	public function getList();

	public function getTripsByCity(Trip $trip, $day, Date $date);
}
