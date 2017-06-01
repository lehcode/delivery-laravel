<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests;

/**
 * Class PaymentInfoRequest
 * @package App\Http\Requests
 */
class PaymentInfoRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			'card_name' => 'required|string',
			'card_number' => 'required|ccn',
			'card_expiry' => 'required|ccd',
			'card_vc' => 'required|cvc',
		];
	}
}
