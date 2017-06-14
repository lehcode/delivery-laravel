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
 *
 * @SWG\Definition(
 *     definition="carrierRegistrationRequest",
 *     required={"name", "email", "phone", "password", "password_confirmation"},
 *     @SWG\Property(property="name", type="string", description="User full name", example="Carrier John Doe"),
 *     @SWG\Property(property="email", type="string", description="User registration email", example="new.carrier@email.com"),
 *     @SWG\Property(property="phone", type="string", description="User mobile number with country prefix", example="+375291234581"),
 *     @SWG\Property(property="password", type="string", description="User defined password", example="SomePassword"),
 *     @SWG\Property(property="password_confirmation", type="string", description="User password confirmation", example="SomePassword")
 * )
 */
class SignupCarrierRequest extends ApiRequest
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
