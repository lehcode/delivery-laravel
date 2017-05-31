<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Carrier;

use App\Models\Trip;

/**
 * Class CarrierService
 * @package App\Services\Carrier
 */
class CarrierService
{
	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}
}
