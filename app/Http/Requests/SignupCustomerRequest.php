<?php

namespace App\Http\Requests;

/**
 * Created by Antony Repin
 * Date: 02.06.2017
 * Time: 18:41
 *
 * Class SignupCustomerRequest
 * @package App\Http\Requests
 *
 * @SWG\Definition(
 *     definition="customerRegistrationRequest",
 *     required={"name", "email", "phone", "password", "password_confirmation"},
 *     @SWG\Property(property="name", type="string", description="User full name", example="Customer John Doe"),
 *     @SWG\Property(property="email", type="string", description="User registration email", example="new.customer@example.com"),
 *     @SWG\Property(property="phone", type="string", description="User mobile number with country prefix", example="+375291234561"),
 *     @SWG\Property(property="password", type="string", description="User defined password", example="SomePassword"),
 *     @SWG\Property(property="password_confirmation", type="string", description="User password confirmation", example="SomePassword")
 * )
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
			//'phone' => 'required|phone:AUTO,mobile',
			'password_confirmation' => 'required|min:5|required_with:password|same:password',
		];
	}
}
