<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Shipment;

use App\Models\Shipment;
use App\Repositories\Shipment\ShipmentRepository;

/**
 * Class CustomerService
 * @package App\Services\Customer
 */
class ShipmentService
{
	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}
}
