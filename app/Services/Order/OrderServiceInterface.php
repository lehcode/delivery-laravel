<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:35
 */

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Interface OrderServiceInterface
 * @package App\Services\Order
 */
interface OrderServiceInterface
{
	/**
	 * @param Request $data
	 *
	 * @return mixed
	 */
	public function create(Request $data);

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
