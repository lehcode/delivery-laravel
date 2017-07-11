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
			'customer' => $this->includeTransformedItem($user, new UserDetailedResponse(false)),
			'recipient' => $this->includeTransformedItem($order->recipient, new RecipientResponse(false)),
			'departure_date' => $order->departure_date,
			'expected_delivery_date' => $order->expected_delivery_date,
			'shipment' => $this->includeTransformedItem($order->shipment, new ShipmentResponse(false)),
			'geo_start' => $order->geo_start,
			'geo_end' => $order->geo_end,
			'price' => $order->price,
			'created_at' => $order->created_at,
		];

		if (isset($order->trip)) {
			$data['trip'] = $this->includeTransformedItem($order->trip, new TripResponse());
		}

		$data['shipment'] = $this->includeTransformedItem($order->shipment, new ShipmentResponse());

		//unset($data['shipment']['size_id']);
		//unset($data['shipment']['category_id']);

		return $data;
	}
}
