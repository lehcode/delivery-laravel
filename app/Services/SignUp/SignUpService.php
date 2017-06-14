<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:48
 */

namespace App\Services\SignUp;

use App\Models\User;
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
		$entityCustomerProfile = array_only($params, app()->make(User\Customer::class)->getFillable());

		$entityUser['type'] = User::ROLE_CUSTOMER;
		$entityUser['is_enabled'] = true;

		Validator::make($params, [
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:5|confirmed',
		])->validate();
		
		return DB::transaction(function () use ($entityUser, $entityCustomerProfile, $params) {

			$user = User::create($entityUser);
			$role = User\Role::where(['name' => 'customer'])->first();
			$user->attachRole($role)->save();

			User\Customer::create(array_merge([ 'id' => $user->id ], $entityCustomerProfile));

			if (isset($params['picture']) && $params['picture'] instanceof UploadedFile) {
				/** @var UploadedFile $picture */
				$picture = $params['picture'];
				$user->profile
					->clearMediaCollection(User::PROFILE_IMAGE)
					->addMedia($picture)
					->toMediaLibrary(User::PROFILE_IMAGE);
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
			'password' => 'required|min:5|confirmed',
		])->validate();

		return User::create($params);
	}

	/**
	 * @param array $params
	 *
	 * @return mixed
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function carrier(array $params)
	{
		$entityUser = array_only($params, app()->make(User::class)->getFillable());
		$entityUser['type'] = User::ROLE_CARRIER;
		$entityUser['is_enabled'] = true;

		$entityProfile = array_only($params, app()->make(User\Carrier::class)->getFillable());
		$entityProfile['is_online'] = false;

		Validator::make($params, [
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:5|confirmed',
		])->validate();

		return DB::transaction(function () use ($entityProfile, $params, $entityUser) {

			$user = User::create($entityUser);
			$role = User\Role::where(['name' => 'carrier'])->first();
			$user->attachRole($role)->save();

			if (isset($params['is_online']) && $params['is_online'] == true) {
				$entityProfile['is_online'] = true;
			} else {
				$entityProfile['is_online'] = false;
			}

			$data = array_merge([ 'id' => $user->id ], $entityProfile);

			User\Carrier::create($data);

			foreach ([User::PROFILE_IMAGE, User\Carrier::ID_IMAGE] as $mediaName) {
				if (isset($params[$mediaName]) && $params[$mediaName] instanceof UploadedFile) {
					/** @var UploadedFile $picture */
					$picture = $params[$mediaName];
					$user->profile
						->clearMediaCollection($mediaName)
						->addMedia($picture)
						->toMediaLibrary($mediaName);
				}
			}

			$this->userService->sendActivationLink($user);

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
