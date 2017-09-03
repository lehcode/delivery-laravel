<?php
/**
 * Created by Antony Repin
 * Date: 9/8/2017
 * Time: 15:48
 */

namespace App\Repositories;


use App\Exceptions\RequestValidationException;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class CrudService
 * @package App\Repositories
 */
class CrudService
{
	/**
	 * @param Request $request
	 * @param null    $id
	 *
	 * @return mixed
	 * @throws RequestValidationException
	 */
	public function setAccountStatus(Request $request, $id = null)
	{
		$key = 'is_enabled';
		$isEnabled = (bool)$request->input($key);

		\Validator::make(['id' => $id, $key => $isEnabled],
			['id' => 'required|regex:' . User::UUID_REGEX, $key => 'required|boolean'])
			->validate();

		$acc = User::where('id', '=', $id)->first();
		$acc->is_enabled = $isEnabled;
		$acc->saveOrFail();

		return ['data' => ["status" => $acc->is_enabled]];

	}
}
