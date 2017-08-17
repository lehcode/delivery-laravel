<?php
/**
 * Created by Antony Repin
 * Date: 8/12/2017
 * Time: 05:52
 */

namespace App\Http\Responses\Admin;


use App\Http\Responses\ApiResponse;
use App\Models\User;

/**
 * Class AdminResponse
 * @package App\Http\Responses\Admin
 */
class AdminResponse extends ApiResponse
{
	/**
	 * @param User $user
	 *
	 * @return array
	 */
	public function transform(User $user)
	{
		$data = $user->toArray();

		//$data['email'] = $data['username'];
		ksort($data);

		return $data;
	}
}
