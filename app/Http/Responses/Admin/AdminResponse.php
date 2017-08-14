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
	 * @param \stdClass $user
	 *
	 * @return array
	 */
	public function transform(\stdClass $user)
	{
		$data = [
			'id' => $user->id,
		];

		return $data;
	}
}
