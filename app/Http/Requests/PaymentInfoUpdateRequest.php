<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 8:12
 */

namespace App\Http\Requests;

/**
 * Class PaymentInfoUpdateRequest
 * @package App\Http\Requests
 */
class PaymentInfoUpdateRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			'card_name' => 'string',
			'card_number' => 'ccn',
			'card_expiry' => 'ccd',
			'card_cvc' => 'cvc',
		];
	}
}
