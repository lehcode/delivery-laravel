<?php
/**
 * Created by Antony Repin
 * Date: 8/22/2017
 * Time: 10:05
 */

namespace App\Http\Responses\Admin;


use App\Http\Responses\ApiResponse;
use App\Http\Responses\PaymentInfoResponse;
use App\Models\User;
use App\Models\User\Customer;
use App\Services\UserService\UserService;

class CustomerResponse extends ApiResponse
{
	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * CustomerResponse constructor.
	 *
	 * @param UserService $userService
	 */
	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}

	/**
	 * @param Customer $customer
	 *
	 * @return array
	 */
	public function transform(Customer $customer)
	{
		$data = [
			'id' => $customer->id,
			'username' => $customer->user->username,
			"phone" => $customer->user->phone,
			"email" => $customer->user->email,
			"name" => !is_null($customer->user->name) ? $customer->user->name : "",
			'is_enabled' => $customer->user->is_enabled,
			User::PROFILE_IMAGE => !is_null($customer->user->photo) ? $customer->user->getMedia(User::PROFILE_IMAGE)->first()->getUrl('thumb') : '',
			"notes" => $customer->notes,
			"cash_payments" => 0,
			"online_payments" => 0,
			"orders" => (int)$this->userService->getUserOrders($customer->id)->count(),
			'payment_info' => isset($customer->card_number) ? $this->includeTransformedItem($customer, new PaymentInfoResponse()) : '',
			'created_at' => $customer->created_at->format('r'),
		];

		if (isset($customer->current_city) && !is_null($customer->current_city)) {
			$data['current_city'] = parent::currentCityFromRole($customer->user, 'customer');
		}

		return $data;
	}
}
