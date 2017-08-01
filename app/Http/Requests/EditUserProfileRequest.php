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
		$rules = [
			'name' => 'string|min:3|regex:/^[^\d]+$/',
			'email' => 'email',
			'phone' => 'phone:AUTO,mobile|unique:users,phone',
			'image' => 'file|image|dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048',
			'id_scan' => 'file|image|dimensions:min_width=1024,min_height=1024,max_width=4096,max_height=4096',
			'location.city' => 'string|min:2|max:64|required_with:location.country',
			'location.country' => 'string|size:2|exists:countries,alpha2_code|required_with:location.city',
			'card_name' => 'string|min:4|max:64',
			'card_number' => 'ccn',
			'card_expiry' => 'date',
			'card_cvc' => 'cvc',
		];
		
		return $rules;
	}
}
