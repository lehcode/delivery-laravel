<?php

namespace App\Http\Requests;

/**
 * Created by Antony Repin
 * Date: 02.06.2017
 * Time: 18:41
 *
 * Class SignupCustomerRequest
 * @package App\Http\Requests
 */
class SignupCustomerRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			'username' => 'required|string|min:3',
			'phone' => 'required|phone:AUTO,mobile',
			'password' => 'required|min:5|confirmed',
			'image' => 'file|image',
			'location.city' => 'string',
			'location.country' => 'string|size:2',
		];
	}
}
