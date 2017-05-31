<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\CrudRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class TripRepository
 * @package App\Repositories\Trip
 */
class OrderRepository extends CrudRepository implements OrderRepositoryInterface
{
	/**
	 * @var
	 */
	protected $model = Order::class;

	/**
	 * @return mixed
	 */
	public function all()
	{
		return Order::all();
	}

	/**
	 * @return mixed
	 */
	public function userOrders()
	{
		$user = Auth::getUser();
		$result = Order::where('customer_id', $user->id);
		return $result;
	}

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function find($id)
	{
		return parent::find($id);
	}
}
