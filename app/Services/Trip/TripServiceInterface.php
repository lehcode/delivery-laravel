<?php

namespace App\Services\Trip;
use App\Models\Trip;

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:35
 */
interface TripServiceInterface
{
	/**
	 * @param array $data
	 *
	 * @return Trip
	 */
	public function create(array $data);

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
	 * @param $id
	 *
	 * @return mixed
	 */
	public function item($id);
}
