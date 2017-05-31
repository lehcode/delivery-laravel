<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:48
 */

namespace App\Services\Customer;

/**
 * Interface CustomerServiceInterface
 * @package App\Services\Customer
 */
interface CustomerServiceInterface
{
	/**
	 * @return mixed
	 */
	public function getNavigation();

	public function getOrders();
}
