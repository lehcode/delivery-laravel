<?php
/**
 * Created by Antony Repin
 * Date: 05.07.2017
 * Time: 15:19
 */

namespace App\Http\Responses;


use App\Models\User\Customer;
use League\Fractal\TransformerAbstract;

/**
 * Class PaymentInfoResponse
 * @package App\Http\Responses
 */
class PaymentInfoResponse extends TransformerAbstract
{

	/**
	 * @param Customer $customer
	 */
	public function transform(Customer $customer)
	{
		$result = [
			'id' => $customer->card_number,
			'card_name' => $customer->card_name,
			'card_number' => $customer->card_number,
			'card_expiry' => $customer->card_date,
			'card_type' => $customer->card_type,
		];
		
		return $result;
	}

}
