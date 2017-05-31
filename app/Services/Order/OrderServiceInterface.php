<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:35
 */

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface OrderServiceInterface
 * @package App\Services\Order
 */
interface OrderServiceInterface
{
	/**
	 * @param array $data
	 *
	 * @return Order
	 */
	public function create(array $data);

	/**
	 * @param Order $order
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function edit(Order $order, array $data);

	/**
	 * @return Collection
	 */
	public function all();

	/**
	 * @param $id
	 *
	 * @return Order
	 */
	public function item($id);
}
