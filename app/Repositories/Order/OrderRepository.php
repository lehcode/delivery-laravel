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
use Jenssegers\Date\Date;
use Propaganistas\LaravelIntl\Facades\Carbon;

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
	public function customerOrders()
	{
		$result = Order::with(['recipient', 'customer', 'shipment', 'trip'])
			->where('customer_id', Auth::getUser()->id)
			->get()
			->map(function ($item) {

				$item->makeHidden(['recipient_id', 'customer_id', 'shipment_id', 'trip_id']);
				$order = $item->toArray();

				$order['expected_delivery_date'] = $item->expected_delivery_date;
				$order['created_at'] = $item->created_at;
				$order['departure_date'] = $item->departure_date;
				$order['customer'] = $item->customer()->with('currentCity')->first()->toArray();
				$order['customer']['current_city']['country'] = $item->customer()->first()->currentCity()->first()->country;

				if (isset($item->trip)){
					$trip = $item->trip()->with(['fromCity', 'destinationCity'])->first();
					$tripClone = $trip->toArray();
					$tripClone['from_city'] = $trip->fromCity()->with('country')->first()->toArray();
					$tripClone['from_city']['country'] = $trip->fromCity()->with('country')->first()->country;
					$tripClone['dest_city'] = $trip->destinationCity()->with('country')->first()->toArray();
					$tripClone['destination_city']['country'] = $trip->destinationCity()->with('country')->first()->country;
					$tripClone['payment_type'] = $trip->paymentType()->first();
					unset($tripClone['from_city_id'], $tripClone['to_city_id'], $tripClone['payment_type_id'], $tripClone['dest_city']);
					$order['trip'] = $tripClone;
				}

				$shipment = $item->shipment()->with(['size', 'category'])->first();
				$shipmentClone = $shipment->toArray();
				$shipmentClone['size'] = $shipment->size()->first();
				$shipmentClone['category'] = $shipment->category()->first();
				unset($shipmentClone['size_id'], $shipmentClone['category_id']);
				$order['shipment'] = $shipmentClone;

				return $order;
			});

		return $result;
	}

	/**
	 * @return mixed
	 */
	public function carrierOrders()
	{
		$result = Order::all('customer_id', Auth::getUser()->id);
		return $result;
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

		$order = factory(Order::class)->create($data);

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
