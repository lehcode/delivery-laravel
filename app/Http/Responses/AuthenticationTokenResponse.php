<?php

/**
	* Created by Antony Repin
	* Date: 24.04.2017
	* Time: 18:24
	*/

namespace App\Http\Responses;

/**
	* Class AuthenticationTokenResponse
	* @package App\Http\Responses
	*/
class AuthenticationTokenResponse extends ApiResponse
{
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function transform(array $data)
	{
		$user = $data[0];
		$token = $data[1];
		$detailed = $data[2];

		$userResponse = $this->includeTransformedItem($user, new UserDetailedResponse($detailed));

		return array_merge([
			'token' => $token,
			'id' => $user->id,
		], $userResponse[0]['attributes']);
	}
}
