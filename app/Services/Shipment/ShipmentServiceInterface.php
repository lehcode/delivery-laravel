<?php

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:55
 */

namespace App\Services\Shipment;

/**
 * Interface ShipmentServiceInterface
 * @package App\Services\Shipment
 */
interface ShipmentServiceInterface
{
	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function create(array $data);
}
