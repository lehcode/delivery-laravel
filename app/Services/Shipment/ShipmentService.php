<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Shipment;

use App\Models\ShipmentCategory;
use App\Models\Trip;

/**
 * Class ShipmentService
 * @package App\Services\Shipment
 */
class ShipmentService implements ShipmentServiceInterface
{
	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getCategories(){
		return ShipmentCategory::all();
	}
}
