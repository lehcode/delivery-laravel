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
use App\Repositories\User\UserRepositoryInterface;
use App\Services\UserService\UserServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Validator;
use DB;
use Webpatser\Uuid\Uuid;

/**
 * Class SignUpService
 * @package App\Services\SignUp
 */
class SignUpService implements SignUpServiceInterface
{
	/**
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * @var UserServiceInterface
	 */
	protected $userService;

	/**
	 * SignUpService constructor.
	 *
	 * @param UserRepositoryInterface $userRepository
	 * @param UserServiceInterface    $userService
	 */
	public function __construct(UserRepositoryInterface $userRepository, UserServiceInterface $userService)
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
		$params = $request->all();
		$entityUser = array_only($params, app()->make(User::class)->getFillable());
		$entityCustomerProfile = array_only($params, app()->make(User\Customer::class)->getFillable());

		$entityUser['type'] = User::ROLE_CUSTOMER;
		$entityUser['is_enabled'] = true;

		if ($request->has('location')) {
			$country = Country::where('alpha2_code', '=', $params['location']['country'])->first();
			$entityCustomerProfile['current_city'] = City::where('name', '=', $params['location']['city'])
				->where('country_id', '=', $country->id)->first()->id;
		}

		Validator::make($params, [
			'username' => 'required|alpha_num|min:3|unique:users,username',
			'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
			'password' => 'required|min:5|alpha_num|confirmed',
			'image' => 'file|image',
			'location.city' => 'string|min:2|max:64',
			'location.country' => 'string|regex:/^[A-Z]{2}$/',
		])->validate();

		return DB::transaction(function () use ($entityUser, $entityCustomerProfile, $params, $request) {

			$user = User::create($entityUser);

			if ($request->has('image') && $request->input('image') instanceof UploadedFile) {
				unset($params['image']);
				$user->clearMediaCollection(User::PROFILE_IMAGE)
					->addMediaFromRequest('image')
					->toMediaCollection(User::PROFILE_IMAGE, 's3');
			}

			$role = User\Role::where(['name' => 'customer'])->first();
			$user->attachRole($role)->saveOrFail();

			$customer = User\Customer::create(array_merge(['id' => $user->id], $entityCustomerProfile));

			if (!$customer->isValid()) {
				$errors = $customer->getErrors()->messages();
				foreach ($errors as $req => $error) {
					foreach ($error as $text) {
						throw new MultipleExceptions($text, 500);
					}
				}
			}

			if ($request->has('email')) {
				$this->userService->sendActivationLink($user);
			} else {
				$user->is_enabled = true;
				$user->saveOrFail();
			}

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

			if ($request->has('email')) {
				$entityUser['email'] = $request->get('email');
			}

			$user = factory(User::class)->make($entityUser);
			
			if (!$user->isValid()) {
				$errors = $user->getErrors();
				foreach ($errors as $req => $error) {
					throw new ModelValidationException($error, 422);
				}
			}

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

			if (isset($data['is_online']) && $data['is_online'] == true) {
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
				$this->userService->sendConfirmationLink($user);
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
