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
	 * @var bool
	 */
	protected $showCardNumber = false;

	/**
	 * PaymentInfoResponse constructor.
	 *
	 * @param bool $showCardNumber
	 */
	public function __construct($showCardNumber = false)
	{
		$this->showCardNumber = $showCardNumber;
	}

	/**
	 * @param Customer $customer
	 */
	public function transform(Customer $customer)
	{
		$result = [
			'id' => substr(md5($customer->card_number . $customer->card_date), 0, 8),
			'card_name' => $customer->card_name,
			'card_number' => $this->showCardNumber === true ? $customer->card_number : $this->scrambleCardNumber($customer->card_number),
			'card_expiry' => $customer->card_date,
			'card_type' => $customer->card_type,
			//'card_cvc' => preg_replace('/\d+/', '***', $customer->card_cvc),
		];

		return $result;
	}

	/**
	 * @param string $number
	 *
	 * @return string
	 */
	private function scrambleCardNumber($number)
	{
		return 'xxxxxxxxxxxx' . substr($number, -4, strlen($number));
	}

}
