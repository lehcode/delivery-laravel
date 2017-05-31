<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Customer;

use App\Models\Trip;

/**
 * Class CustomerService
 * @package App\Services\Customer
 */
class CustomerService
{
	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}
}
