<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:22
 */

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\CrudRepositoryInterface;

/**
 * Interface OrderRepositoryInterface
 * @package App\Repositories
 */
interface OrderRepositoryInterface extends CrudRepositoryInterface
{

	/**
	 * @param array $data
	 *
	 * @return Order
	 */
	public function create(array $data);

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function find($id);

	/**
	 * @return mixed
	 */
	public function all();

}
