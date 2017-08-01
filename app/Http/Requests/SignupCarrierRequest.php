<?php
/**
 * Created by Antony Repin
 * Date: 02.06.2017
 * Time: 18:41
 */

namespace App\Http\Requests;

/**
 *
 * Class SignupCustomerRequest
 * @package App\Http\Requests
 */
class SignupCarrierRequest extends ApiRequest
{
	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			'username' => 'required|string|min:3|unique:users,username',
			'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
			'password' => 'required|min:6|alpha_num|confirmed',
			'image' => 'file|image',
		];
	}
}
