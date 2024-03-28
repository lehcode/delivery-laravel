<?php
/**
 * Created by Antony Repin
 * Date: 8/15/2017
 * Time: 19:39
 */

namespace App\Http\Requests\Admin;


use App\Http\Requests\ApiRequest;

/**
 * Class SignupAdminRequest
 * @package App\Http\Requests\Admin
 */
class SignupAdminRequest extends ApiRequest
{
	/**
	 * array
	 */
	const RULES = [
		'username' => 'required|email|unique:users,username',
		'name' => 'required|string|min:3',
		'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
		'role' => 'required|in:admin,support,accountant',
		'password' => 'required|alpha_num|confirmed',
		'password_confirmation' => 'required|alpha_num|required_with:password',
		'is_enabled' => 'nullable|boolean',
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
		]);

		if (env('APP_DEBUG')){
			$rules['password'] .= '|min:3';
			$rules['password_confirmation'] .= '|min:3';
		} else {
			$rules['password'] .= '|min:8';
			$rules['password_confirmation'] .= '|min:8';
		}

		return $rules;
	}
}
