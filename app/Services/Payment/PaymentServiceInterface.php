<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:35
 */

namespace App\Services\Payment;

use App\Models\Payment;

/**
 * Interface PaymentServiceInterface
 * @package App\Services\Payment
 */
interface PaymentServiceInterface
{
	/**
	 * @param array $data
	 *
	 * @return Trip
	 */
	public function create(array $data);

	/**
	 * @param Payment $payment
	 * @param array   $params
	 *
	 * @return mixed
	 */
	public function edit(Payment $payment, array $params);

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
