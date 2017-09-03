<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\ApiRequest;

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
			'customer' => 'required|string|exists:users,username',
			'start_coord' => 'required',
			'end_coord' => 'required',
			'recipient_name' => 'required|string',
			'recipient_phone' => 'required|phone:AUTO,mobile',
			'recipient_notes' => 'nullable',
			'shipment_size' => 'required|numeric|exists:shipment_sizes,id',
			'shipment_category' => 'required|numeric|exists:shipment_categories,id',
			'images' => 'array|required'
		];

		return $rules;
	}
}
