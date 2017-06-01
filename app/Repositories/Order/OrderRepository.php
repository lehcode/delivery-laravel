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
		$result = Order::all();
		return $result;
	}

	/**
	 * @return mixed
	 */
	public function userOrders()
	{
		$result = Order::where('customer_id', Auth::getUser()->id);
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

	/**
	 * @param array $data
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function create(array $data)
	{

		if (isset($data['XDEBUG_SESSION_START'])){
			unset($data['XDEBUG_SESSION_START']);
		}

		$data['customer_id'] = Auth::getUser()->customer()->id;

		$order = factory(Order::class)->create($data);

		if (!is_null($order->validationErrors)) {
			throw new \Exception($order->validationErrors->getMessages());
		}
		
		return $order;
	}
}
