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

	const RULES = [
		'username' => 'required|alpha_num|min:3|unique:users,username',
		'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
		'password' => 'required|min:5|alpha_num|confirmed',
		'password_confirmation' => 'required|min:6|alpha_num|required_with:password',
	];

	/**
	 * @return array
	 */
	public function rules()
	{

		$rules = self::RULES;

		$rules = array_merge($rules, [
			'email' => 'nullable|email|unique:users,email',
			'photo' => 'nullable|file|image|dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048',
			'id_scan' => 'nullable|file|image|dimensions:min_width=1024,min_height=1024,max_width=4096,max_height=4096',
			'birthday' => 'date|nullable',
			'nationality' => 'string|nullable|min:2',
			'id_number' => 'string|required|min:3',
		]);

		if (env('APP_DEBUG')) {
			$rules['id_scan'] = 'nullable';
			$rules['photo'] = 'nullable';
			$rules['id_number'] = 'string|nullable|min:3';
		}

		return $rules;
	}
}
