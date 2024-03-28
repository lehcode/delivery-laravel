<?php
/**
 * Created by Antony Repin
 * Date: 26.06.2017
 * Time: 15:07
 */

namespace App\Http\Requests;


class RecipientRequest extends ApiRequest
{
	const RULES = [
		'name' => 'required|string|min:3',
		'phone' => 'required|phone:AUTO,mobile',
	];
	/**
	 * @return array
	 */
	public function rules()
	{
		return self::RULES;
	}
}
