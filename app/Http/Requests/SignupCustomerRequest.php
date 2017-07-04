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
			'name' => 'required|min:3',
			'email' => 'required|email',
			'password' => 'required|min:5|confirmed',
			'image' => 'required|file|image',
			'location.city' => 'required|string',
			'location.country' => 'required|string',
		];
	}
}
