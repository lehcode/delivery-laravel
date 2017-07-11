<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Order;

use App\Exceptions\MultipleExceptions;
use App\Models\Order;
use App\Repositories\CrudRepository;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

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
	public function customerOrders()
	{
		return Order::where('customer_id', Auth::getUser()->id)->get();
	}

	/**
	 * @return mixed
	 */
	public function carrierOrders()
	{
		return Order::all('carrier_id', Auth::getUser()->id);
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function create(array $data)
	{

		$data['customer_id'] = Auth::user()->customer->id;
		$data['status'] = Order::STATUS_CREATED;

		try{
			$order = factory(Order::class)->create($data);
		} catch (\Exception $e){
			switch ($e->getCode()){
				case 23000:
					throw new MultipleExceptions("Duplicate shipment found.", 422);
					break;
				default:
					throw new MultipleExceptions("Cannot create Order.", $e->getCode(), $e);
			}
		}


		if (!$order->isValid()){
			$errors = $order->getErrors();
			foreach ($errors as $error){
				throw new \Exception($error, 404);
			}
		}

		return $order;
	}

	/**
	 * @param $id
	 * @param $status
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function updateStatus($id, $status)
	{
		$updated = Order::findOrFail($id)
			->fill(['status' => $status]);

		try {
			$updated->save();
			return $updated;
		} catch (\Exception $e) {
			throw $e;
		}

	}
}
