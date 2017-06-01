<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests;


/**
 * Class TripRequest
 * @package App\Http\Requests
 */
class TripRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			'payment_type_id' => 'required|integer',
			'carrier_id' => 'required|string',
			'from_city_id' => 'required|integer',
			'to_city_id' => 'required|integer',
			'departure_date' => 'required|date'
		];
	}
}
