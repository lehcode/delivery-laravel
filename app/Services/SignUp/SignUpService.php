<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:48
 */

namespace App\Services\SignUp;

use App\Exceptions\ModelValidationException;
use App\Exceptions\MultipleExceptions;
use App\Exceptions\RequestValidationException;
use App\Http\Requests\Admin\SignupAdminRequest;
use App\Http\Requests\SignupCarrierRequest;
use App\Http\Requests\SignupCustomerRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Models\UserSignupRequest;
use App\Repositories\User\UserRepository;
use App\Services\UserService\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Validator;
use DB;

/**
 * Class SignUpService
 * @package App\Services\SignUp
 */
class SignUpService implements SignUpServiceInterface
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * SignUpService constructor.
	 *
	 * @param UserRepository $userRepository
	 * @param UserService    $userService
	 */
	public function __construct(UserRepository $userRepository,
	                            UserService $userService)
	{
		$this->userService = $userService;
		$this->userRepository = $userRepository;
	}

	/**
	 * @param SignupCustomerRequest $request
	 *
	 * @return mixed
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function customer(SignupCustomerRequest $request)
	{
		$entityUser = array_only($request->all(), app()->make(User::class)->getFillable());
		$entityCustomerProfile = array_only($request->all(), app()->make(User\Customer::class)->getFillable());

		$entityUser['is_enabled'] = $request->has('is_enabled') ? (int)$request->input('is_enabled') : false;

		if ($request->has('location')) {
			$data['country'] = $request->input('location.country');
			$data['city'] = $request->input('location.city');
			$country = Country::where('alpha2_code', '=', $data['country'])->first();
			$entityCustomerProfile['current_city'] = City::where('name', '=', $data['city'])
				->where('country_id', '=', $country->id)->first()->id;
		}

		Validator::make($request->all(), $request->rules())->validate();

		return DB::transaction(function () use ($entityUser, $entityCustomerProfile, $request) {

			$user = factory(User::class)->make($entityUser);

			if (!$user->isValid()) {
				$errors = $user->getErrors();
				foreach ($errors as $req => $error) {
					throw new ModelValidationException($error, 422);
				}
			}

			$user->saveOrFail();

			$role = User\Role::where(['name' => 'customer'])->first();
			$user->attachRole($role->id);
			$user->saveOrFail();
			$customer = User\Customer::create(array_merge(['id' => $user->id], $entityCustomerProfile));

			if (!$customer->isValid()) {
				$errors = $customer->getErrors()->messages();
				foreach ($errors as $req => $error) {
					foreach ($error as $text) {
						throw new MultipleExceptions($text, 500);
					}
				}
			}

			if ($request->has(User::PROFILE_IMAGE) && $request->input(User::PROFILE_IMAGE) instanceof UploadedFile) {
				$user->clearMediaCollection(User::PROFILE_IMAGE)
					->addMediaFromRequest(User::PROFILE_IMAGE)
					->toMediaCollection(User::PROFILE_IMAGE, 's3');
			}

			if ($request->has('email')) {
				$this->userService->sendActivationLink($user);
			}

			$customer->saveOrFail();
			$user->saveOrFail();

			return $user;
		});
	}

	/**
	 * @param SignupAdminRequest $request
	 *
	 * @return User mixed
	 * @throws RequestValidationException
	 */
	public function admin(SignupAdminRequest $request)
	{
		if ($request->validate() !== true) {
			throw new RequestValidationException($request->messages());
		}

		$data = $request->all();
		$entityUser = array_only($data, app()->make(User::class)->getFillable());
		$entityUser['password'] = $request->input('password');

		return DB::transaction(function () use ($entityUser, $request) {

			$user = factory(User::class)->make($entityUser);

			if (!$user->isValid()) {
				$errors = $user->getErrors();
				foreach ($errors as $req => $error) {
					throw new ModelValidationException($error, 422);
				}
			}

			$user->email = $request->input('username');

			$user->saveOrFail();

			$role = User\Role::where(['name' => strtolower($request->input('role'))])->first();
			$user->attachRole($role);

			if ($request->has(User::PROFILE_IMAGE)) {
				if ($request->input(User::PROFILE_IMAGE) instanceof UploadedFile) {
					$img = $request->input(User::PROFILE_IMAGE);
					$user->clearMediaCollection(User::PROFILE_IMAGE)
						->addMedia($img)
						->usingFileName($img->hashName())
						->toMediaCollection(User::PROFILE_IMAGE, 's3');
				}
			}

			if ($request->has('email')) {
				$this->userService->sendConfirmationLink($user);
			}

			return $user;

		});
	}

	/**
	 * @param SignupCarrierRequest $request
	 *
	 * @return mixed
	 * @throws RequestValidationException
	 */
	public function carrier(SignupCarrierRequest $request)
	{
		$data = $request->all();
		$entityUser = array_only($data, app()->make(User::class)->getFillable());
		$entityUser['is_enabled'] = true;

		$entityCarrier = array_only($data, app()->make(User\Carrier::class)->getFillable());
		$entityCarrier['is_online'] = false;

		if ($request->validate() !== true) {
			throw new RequestValidationException($request->messages());
		}

		return DB::transaction(function () use ($entityCarrier, $data, $entityUser, $request) {

			if ($request->has('email')) {
				$entityUser['email'] = $data['email'];
			}

			$user = factory(User::class)->make($entityUser);

			if (!$user->isValid()) {
				$errors = $user->getErrors();
				foreach ($errors as $req => $error) {
					throw new ModelValidationException($error, 422);
				}
			}

			$role = User\Role::where(['name' => 'carrier'])->first();

			if ($request->has('is_enabled') && ((bool)$request->input('is_enabled') === true)) {
				$entityUser['is_enabled'] = true;
			} else {
				$entityUser['is_enabled'] = false;
			}

			if ($request->has('is_online') && ((bool)$request->input('is_online') === true)) {
				$entityCarrier['is_online'] = true;
			} else {
				$entityCarrier['is_online'] = false;
			}

			$user->saveOrFail();
			$user->attachRole($role);

			$carrier = factory(User\Carrier::class)
				->create(array_merge(['id' => $user->id], $entityCarrier));

			if (!$carrier->isValid()) {
				$errors = $carrier->getErrors();
				foreach ($errors as $req => $error) {
					throw new ModelValidationException($error, 422);
				}
			}

			if ($request->has(User\Carrier::ID_IMAGE)) {
				if ($data[User\Carrier::ID_IMAGE] instanceof UploadedFile) {
					$img = $data[User\Carrier::ID_IMAGE];
					$user->carrier->clearMediaCollection(User\Carrier::ID_IMAGE)
						->addMedia($img)
						->usingFileName($img->hashName())
						->toMediaCollection(User\Carrier::ID_IMAGE, 's3');
				}
			}

			if ($request->has(User::PROFILE_IMAGE)) {
				if ($data[User::PROFILE_IMAGE] instanceof UploadedFile) {
					$img = $data[User::PROFILE_IMAGE];
					$user->clearMediaCollection(User::PROFILE_IMAGE)
						->addMedia($img)
						->usingFileName($img->hashName())
						->toMediaCollection(User::PROFILE_IMAGE, 's3');
				}
			}

			if ($request->has('email')) {
				$this->userService->sendActivationLink($user);
			}

			$carrier->saveOrFail();
			$user->saveOrFail();
			$user->load('carrier');

			return $user;
		});
	}

	/**
	 * @param array $params
	 *
	 * @return Model|UserSignupRequest|null
	 */
	public function request(array $params)
	{
		Validator::make($params, [
			'email' => 'required|email|unique:users,email|unique:user_signup_requests,email',
			'phone' => 'required|phone:AUTO|unique:users,phone|unique:user_signup_requests,phone',
			'name' => 'required'
		])->validate();

		return UserSignupRequest::create(array_merge(array_only($params, ['email', 'phone', 'name', 'notes']), [
			'is_processed' => false
		]));
	}
}
