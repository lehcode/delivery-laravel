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
		$customer = $user->customer()->first();

		$data = [
			'id' => $order->id,
			'customer' => $customer,
			'recipient' => $order->recipient,
			'departure_date' => $order->departure_date,
			'expected_delivery_date' => $order->expected_delivery_date,
			'shipment' => $order->shipment,
			'carrier' => $order->trip->carrier()->with('currentCity')->first(),
			'from_city' => $order->trip->fromCity()->with('country')->first(),
			'to_city' => $order->trip->destinationCity()->with('country')->first(),
			'geo_start' => $order->geo_start,
			'geo_end' => $order->geo_end,
			'payment_type' => $order->trip->paymentType,
			'created_at' => $order->created_at,
			'updated_at' => $order->updated_at,
		];

		return $data;
	}
}
