<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 4:05
 */

namespace App\Http\Responses;

use App\Models\Order;
use App\Models\User;

/**
 * Class OrderResponse
 * @package App\Http\Responses
 */
class OrderResponse extends ApiResponse
{
	/**
	 * @param Order $order
	 *
	 * @return array
	 */
	public function transform(Order $order)
	{

		$user = User::where(['id' => $order->customer_id])->first();

		$data = [
			'id' => $order->id,
			'status' => $order->status,
			'customer' => $user->customer()->with('currentCity')->first()->toArray(),
			'recipient' => $order->recipient,
			'departure_date' => $order->departure_date,
			'expected_delivery_date' => $order->expected_delivery_date,
			'shipment' => $order->shipment->toArray(),
			'carrier' => $order->trip->carrier->with('currentCity')->first()->toArray(),
			'from_city' => $order->trip->fromCity()->with('country')->first()->toArray(),
			'dest_city' => $order->trip->destinationCity->with('country')->first()->toArray(),
			'geo_start' => $order->geo_start,
			'geo_end' => $order->geo_end,
			'payment_type' => $order->trip->paymentType,
			'created_at' => $order->created_at,
			'updated_at' => $order->updated_at,
		];

		$data['customer']['current_city']['country'] = $user->customer()->with('currentCity')->first()
			->currentCity()->with('country')->first()->country->toArray();
		$data['from_city']['country'] = $order->trip->fromCity()->with('country')->first()->country;
		$data['dest_city']['country'] = $order->trip->destinationCity()->with('country')->first()->country;
		$data['shipment']['size'] = $order->shipment->size()->first();
		$data['shipment']['category'] = $order->shipment->category()->first();
		$data['carrier']['current_city']['country'] = $order->trip->carrier->with('currentCity')->first()
			->currentCity()->with('country')->first()->country->toArray();

		unset($data['shipment']['size_id'], $data['shipment']['category_id']);

		return $data;
	}
}
