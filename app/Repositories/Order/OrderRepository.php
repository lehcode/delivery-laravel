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
			->get(['id'])
			->map(function ($item) {

				$item->makeHidden(['recipient_id', 'customer_id', 'shipment_id', 'trip_id']);
				$clone = $item->toArray();
				
				$clone['expected_delivery_date'] = Date::createFromFormat($item->dateFormat, $clone['expected_delivery_date']);
				$clone['created_at'] = Date::createFromFormat($item->dateFormat, $clone['created_at']);
				$clone['updated_at'] = Date::createFromFormat($item->dateFormat, $clone['updated_at']);
				$clone['departure_date'] = Date::createFromFormat($item->dateFormat, $clone['departure_date']);
				$clone['customer'] = $item->customer()->with('currentCity')->first()->toArray();
				$clone['customer']['current_city']['country'] = $item->customer()->first()->currentCity()->first()->country;

				$trip = $item->trip()->with(['fromCity', 'destinationCity'])->first();
				$tripClone = $trip->toArray();
				$tripClone['from_city'] = $trip->fromCity()->with('country')->first();
				$tripClone['from_city']['country'] = $trip->fromCity()->with('country')->first()->country;
				$tripClone['dest_city'] = $trip->destinationCity()->with('country')->first();
				$tripClone['dest_city']['country'] = $trip->destinationCity()->with('country')->first()->country;
				unset($tripClone['from_city_id'], $tripClone['to_city_id']);
				$clone['trip'] = $tripClone;
				
				$shipment = $item->shipment()->with(['size', 'category'])->first();
				$shipmentClone = $shipment->toArray();
				$shipmentClone['size'] = $shipment->size()->first();
				$shipmentClone['category'] = $shipment->category()->first();
				unset($shipmentClone['size_id'], $shipmentClone['category_id']);
				$clone['shipment'] = $shipmentClone;

				return $clone;
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

		$order = factory(Order::class)->create($data);

		if (!is_null($order->validationErrors)) {
			throw new \Exception($order->validationErrors->getMessages());
		}

		return $order;
	}
}
