<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:48
 */

namespace App\Services\SignUp;

use App\Models\ProfileCustomer;
use App\Models\ProfileDriver;
use App\Models\User;
use App\Models\User\Customer;
use App\Models\User\Role;
use App\Models\UserSignupRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\UserService\UserServiceInterface;
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
	 * @param array $params
	 *
	 * @return Model|User|null
	 */
	public function customer(array $params)
	{

		$entityUser = array_only($params, app()->make(User::class)->getFillable());
		$entityCustomerProfile = array_only($params, app()->make(Customer::class)->getFillable());

		$entityUser['type'] = User::ROLE_CUSTOMER;
		$entityUser['is_enabled'] = true;


		return DB::transaction(function () use ($entityUser, $entityCustomerProfile, $params) {

			$user = User::create($entityUser);

			$role = Role::where(['name' => 'customer'])->first();

			$user->attachRole($role)->save();

			Customer::create(array_merge([
				'user_id' => $user->id
			], $entityCustomerProfile));

			if (isset($params['picture']) && $params['picture'] instanceof UploadedFile) {
				/** @var UploadedFile $picture */
				$picture = $params['picture'];
				$user->profile
					->clearMediaCollection(ProfileCustomer::MEDIA_PICTURE)
					->addMedia($picture)
					->toMediaLibrary(ProfileCustomer::MEDIA_PICTURE);
			}

			$this->userService->sendActivationLink($user);

			return $user;
		});
	}

	/**
	 * @param array $params
	 *
	 * @return Model|User|null
	 */
	public function admin(array $params)
	{
		$params = array_merge($params, [
			'type' => User::TYPE_ADMIN,
			'is_enabled' => true
		]);

		Validator::make($params, [
			'email' => 'required|email|unique:users,email',
			'phone' => 'required|phone:AUTO|unique:users,phone',
			'password' => 'required|min:5|confirmed',
		])->validate();

		return User::create($params);
	}

	public function driver(array $params)
	{
		$entityUser = array_only($params, app()->make(User::class)->getFillable());
		$entityProfile = array_only($params, app()->make(ProfileDriver::class)->getFillable());
		$entityCar = isset($params['car']) && is_array($params['car']) ? $params['car'] : [];

		$entityUser = array_merge($entityUser, [
			'type' => User::TYPE_DRIVER
		]);

		Validator::make($params, [
			'phone' => 'required|phone:AUTO',
			'name' => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:5',
			'language' => 'array',
			'is_enabled' => 'required|boolean',
			'referer' => 'min:1',

			'car.name' => 'required',
			'car.model' => 'required',
			'car.type' => 'required',
			'car.plate' => 'required',
			'car.seats' => 'required|integer|min:1',

			'cash_limit' => 'required|integer:min:1',
			'membership_id' => 'required|exists:memberships,id',
			'manager_id' => 'min:1',
			'notes' => 'min:1',

			ProfileDriver::MEDIA_ID_CARD => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
			ProfileDriver::MEDIA_LICENSE => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
			ProfileDriver::MEDIA_PICTURE => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',

			'car.' . UserCar::MEDIA_CAR => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
			'car.' . UserCar::MEDIA_CAR_PLATE => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
		])->validate();

		return DB::transaction(function () use ($entityUser, $entityProfile, $entityCar, $params) {
			/** @var User $user */
			$user = User::create($entityUser);

			if (isset($params['language']) && is_array($params['language'])) {
				$language_ids = array_map(function ($value) {
					return $value['id'];
				}, $params['language']);

				$user->languages()->sync($language_ids);
			}

			$user->memberships()->sync([$params['membership_id']]);

			if (isset($params['is_online']) && $params['is_online'] == true) {
				$entityProfile['status'] = ProfileDriver::STATUS_ONLINE;
			} else {
				$entityProfile['status'] = ProfileDriver::STATUS_OFFLINE;
			}

			ProfileDriver::create(array_merge([
				'user_id' => $user->id
			], $entityProfile));

			UserCar::create(array_merge([
				'user_id' => $user->id
			], $entityCar));

			foreach ([ProfileDriver::MEDIA_PICTURE, ProfileDriver::MEDIA_LICENSE, ProfileDriver::MEDIA_ID_CARD] as $mediaName) {
				if (isset($params[$mediaName]) && $params[$mediaName] instanceof UploadedFile) {
					/** @var UploadedFile $picture */
					$picture = $params[$mediaName];
					$user->profile
						->clearMediaCollection($mediaName)
						->addMedia($picture)
						->toMediaLibrary($mediaName);
				}
			}

			foreach ([UserCar::MEDIA_CAR, UserCar::MEDIA_CAR_PLATE] as $mediaName) {
				if (isset($entityCar[$mediaName]) && $entityCar[$mediaName] instanceof UploadedFile) {
					/** @var UploadedFile $picture */
					$picture = $entityCar[$mediaName];
					$user->car
						->clearMediaCollection($mediaName)
						->addMedia($picture)
						->toMediaLibrary($mediaName);
				}
			}

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
