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
			'password' => 'required|min:5',
			'password_confirmation' => 'required|min:5|required_with:password|same:password',
			'phone' => 'required|phone:AUTO,mobile',
			'picture' => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
		];
	}
}
