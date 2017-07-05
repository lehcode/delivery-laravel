<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Customer;

use App\Exceptions\MultipleExceptions;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Requests\PaymentInfoRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

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

		if (!$request->validate()) {
			throw new MultipleExceptions("Request validation failed", 401);
		}

		return \DB::transaction(function () use ($request) {

			$user = \Auth::user();
			$data = $request->all();

			if ($request->has('remove_image')) {
				$user->clearMediaCollection(User::PROFILE_IMAGE);
			}

			if ($request->has('name')) {
				$user->customer->name = $data['name'];
			}

			if ($request->has('image') && $request->input('image') instanceof UploadedFile) {
				unset($data['image']);
				$user->clearMediaCollection(User::PROFILE_IMAGE)
					->addMediaFromRequest('image')
					->toMediaCollection(User::PROFILE_IMAGE, 's3');
			}

			if ($request->has('location.city')) {
				$country = Country::where('alpha2_code', '=', $request->input('location.country'))->first();
				$user->customer->current_city = City::where('name', '=', $request->input('location.city'))
					->where('country_id', '=', $country->id)
					->first()->id;

			}

			$user->customer->saveOrFail();

			if (count($data)) {
				$user->fill($data)->saveOrFail();
			}

			return $user;
		});

	}

	/**
	 * @param PaymentInfoRequest $request
	 *
	 * @return mixed
	 */
	public function savePaymentInfo(PaymentInfoRequest $request)
	{
		$data = $request->all();
		\Auth::user()->customer->fill($data)->save();
		return \Auth::user()->customer;

	}
}
