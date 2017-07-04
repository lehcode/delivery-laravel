<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests;

/**
 * Class OrderRequest
 * @package App\Http\Requests
 */
class OrderRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'recipient_id' => 'required',
			'shipment_id' => 'required',
			'trip_id' => '',
			'expected_delivery_date' => 'required',
			'geo_start' => 'required|array',
			'geo_end' => 'required|array',
			'price' => 'required|numeric',
		];
	}
}
