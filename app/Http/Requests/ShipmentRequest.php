<?php
/**
 * Created by Antony Repin
 * Date: 26.06.2017
 * Time: 5:39
 */

namespace App\Http\Requests;

/**
 * Class ShipmentRequest
 * @package App\Http\Requests
 */
class ShipmentRequest extends ApiRequest
{
	const RULES = [
		'category_id' => 'required|integer',
		'size_id' => 'required|integer',
	];
	/**
	 * @return array
	 */
	public function rules()
	{
		return self::RULES;
	}
}
