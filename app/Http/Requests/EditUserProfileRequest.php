<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 18:30
 */

namespace App\Http\Requests;

/**
 * Class EditUserProfileRequest
 * @package App\Http\Requests
 */
class EditUserProfileRequest extends ApiRequest
{
	/**
	 * @return array
	 *
	 */
	public function rules()
	{
		return [
			'name' => 'min:3',
			'email' => 'email',
			'password' => 'required|min:5',
			'password_confirmation' => 'required|min:5|required_with:password',
			'phone' => 'phone:AUTO,mobile',
			'picture' => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
		];
	}
}
