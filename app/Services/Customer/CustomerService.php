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
use App\Services\CrudServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

/**
 * Class CustomerService
 * @package App\Services\Customer
 */
class CustomerService implements CrudServiceInterface
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
	 * @return User|null
	 * @throws MultipleExceptions
	 */
	public function update(EditUserProfileRequest $request)
	{

		if (!$request->validate()) {
			throw new MultipleExceptions("Request validation failed", 401);
		}

		return \DB::transaction(function () use ($request) {

			$user = \Auth::user();
			$user->load('customer');
			$data = $request->all();

			if ($request->has('remove_image')) {
				$user->clearMediaCollection(User::PROFILE_IMAGE);
			}

			if ($request->has('name')) {
				$user->name = $data['name'];
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

	/**
	 * Get Collection of all Customers
	 *
	 * @return Collection
	 */
	public function all()
	{
		return User\Customer::all();
	}

	/**
	 * Get single Customer model
	 *
	 * @param Request $request
	 * @param string  $id
	 *
	 * @return mixed
	 */
	public function byId(Request $request, $id)
	{

		\Validator::make(['id' => $id], ['id' => 'required|regex:' . User::UUID_REGEX])
			->validate();

		return User::where('id', '=', $id)->first();
	}

	/**
	 * @param Request $request
	 * @param string  $id
	 *
	 * @return mixed
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function setAccountStatus(Request $request, $id)
	{
		\Validator::make([
			'id' => $id,
			'enabled' => (int)$request->input('enabled'),
		], [
			'id' => 'required|regex:' . User::UUID_REGEX,
			'enabled' => 'required|in:0,1',
		])->validate();

		$acc = User::where('id', '=', $id)->first();
		$acc->is_enabled = (int)$request->input('enabled');
		$acc->saveOrFail();

		return $acc;

	}


}
