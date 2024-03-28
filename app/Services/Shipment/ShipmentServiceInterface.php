<?php

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:55
 */

namespace App\Services\Shipment;

use App\Http\Requests\ShipmentRequest;

/**
 * Interface ShipmentServiceInterface
 * @package App\Services\Shipment
 */
interface ShipmentServiceInterface
{
	/**
	 * @param ShipmentRequest $request
	 *
	 * @return mixed
	 */
	public function create(ShipmentRequest $request);
}
