<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Customer;

use App\Exceptions\MultipleExceptions;
use App\Http\Requests\EditUserProfileRequest;
use App\Models\Trip;
use Illuminate\Http\UploadedFile;

/**
 * Class CustomerService
 * @package App\Services\Customer
 */
class CustomerService
{
	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}

	/**
	 * @param EditUserProfileRequest $request
	 *
	 * @return \App\Models\User|null
	 * @throws MultipleExceptions
	 */
	public function update(EditUserProfileRequest $request)
	{

		try {
			$request->validate();
		} catch (\Exception $e){
			throw new MultipleExceptions($e->getMessage(), 500);
		}

		$user = \Auth::user();

		if ($request->has('image') && $request->input('image') instanceof UploadedFile){
			$image = $request->input('image');
		}

		$data = $request->all();
		
		return $user;
		
	}
}
