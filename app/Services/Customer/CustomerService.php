<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Customer;

use App\Exceptions\MultipleExceptions;
use App\Exceptions\RequestValidationException;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Requests\PaymentInfoRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use App\Services\CrudServiceInterface;
use App\Services\UserService\UserService;
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
	protected $userService;

	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}

	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param string                 $user_id
	 *
	 * @return mixed
	 * @throws MultipleExceptions
	 */
	public function update(EditUserProfileRequest $request, $user_id = null)
	{

		if (!$request->validate()) {
			throw new MultipleExceptions("Request validation failed", 401);
		}

		$user = is_null($user_id) ? \Auth::user() : User::where('id', '=', $user_id)->get()->first();

		return \DB::transaction(function () use ($request, $user) {

			$user->load('customer');
			$data = $request->all();

			if ($request->has('remove_image')) {
				$user->clearMediaCollection(User::PROFILE_IMAGE);
			}

			if ($request->has('name')) {
				$user->name = $data['name'];
			}

			if ($request->has(User::PROFILE_IMAGE) && $request->input(User::PROFILE_IMAGE) instanceof UploadedFile) {
				unset($data['image']);
				$user->clearMediaCollection(User::PROFILE_IMAGE)
					->addMediaFromRequest(User::PROFILE_IMAGE)
					->toMediaCollection(User::PROFILE_IMAGE, 's3');
			}

			if ($request->has('location.city')) {
				$country = Country::where('alpha2_code', '=', $request->input('location.country'))->first();
				$user->customer->current_city = City::where('name', '=', $request->input('location.city'))
					->where('country_id', '=', $country->id)
					->first()->id;
				$user->customer->saveOrFail();
			}

			if ($request->has('phone') && $request->input('phone') !== $user->phone) {
				$this->userService->sendConfirmationSms($user);
				unset($data['phone']);
			}

			if ($request->has('email') && $request->input('email') !== $user->email) {
				$this->userService->sendConfirmationLink($user);
				unset($data['email']);
			}

			if ($request->has('is_enabled')) {
				$user->is_enabled = $request->input('is_enabled');
			}

			$user->fill($data)->saveOrFail();

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




}
