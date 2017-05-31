<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:48
 */

namespace App\Services\Carrier;

/**
 * Interface CustomerServiceInterface
 * @package App\Services\Customer
 */
interface CarrierServiceInterface
{
	/**
	 * @return mixed
	 */
	public function getNavigation();

	public function getOrders();
	
	public function getTrips();
}
