<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests;


class TripRequest extends ApiRequest
{
	public function rules()
	{
		return [
			'payment_type_id' => 'required|numeric',
			'carrier_id' => 'required|numeric',
			'from_city_id' => 'required|numeric',
			'to_city_id' => 'required|numeric',
			'departure_date' => 'required|date'
		];
	}
}
