<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:48
 */

namespace App\Services\UserService;

use App\Exceptions\MultipleExceptions;
use App\Http\Requests\EditUserProfileRequest;
use App\Mail\EmailConfirmationMail;
use App\Mail\UserActivationMail;
use App\Mail\UserPasswordResetMail;
use App\Models\Order;
use App\Models\ProfileCustomer;
use App\Models\ProfileDriver;
use App\Models\User;
use App\Models\UserCar;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Mail;
use Hash;

class UserService implements UserServiceInterface
{
	/**
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * UserService constructor.
	 *
	 * @param UserRepositoryInterface $userRepository
	 */
	public function __construct(UserRepositoryInterface $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param User  $user
	 * @param array $params
	 *
	 * @return bool
	 */
	public function edit(User $user, array $params)
	{
		$funcName = 'edit' . ucfirst($user->roles()->first()->name);
		return $this->$funcName($user, $params);
	}

	/**
	 * @param User  $user
	 * @param array $params
	 *
	 * @return bool
	 */
	protected function editCustomer(User $user, array $params)
	{
		$entityUser = array_only($params, $user->getFillable());
		$entityProfile = null;

		if (!is_null($user->profile)) {
			$entityProfile = array_only($params, $user->profile->getFillable());
		}

		Validator::make($params, [
			'email' => ['email', Rule::unique('users')->ignore($user->id)],
			'password' => 'min:5',
			'phone' => ['phone:AUTO', Rule::unique('users')->ignore($user->id)],
		])->validate();

		return DB::transaction(function () use ($entityUser, $entityProfile, $user, $params) {
			$this->userRepository->edit($user, $entityUser);

			if (!is_null($user->profile)) {
				$this->userRepository->edit($user->profile, $entityProfile);

				if (isset($params['remove_picture'])) {
					$user->profile->clearMediaCollection(User::PROFILE_IMAGE);
				}

				if (isset($params['picture']) && $params['picture'] instanceof UploadedFile) {
					/** @var UploadedFile $picture */
					$picture = $params['picture'];
					$user->profile
						->clearMediaCollection(User::PROFILE_IMAGE)
						->addMedia($picture)
						->toMediaLibrary(User::PROFILE_IMAGE);
				}
			}

			return true;
		});
	}

	/**
	 * @param User  $user
	 * @param array $params
	 *
	 * @return bool
	 */
	protected function editAdmin(User $user, array $params)
	{
		Validator::make($params, [
			'email' => ['email', Rule::unique('users')->ignore($user->id)],
			'password' => 'min:5|confirmed',
			'phone' => ['phone:AUTO', Rule::unique('users')->ignore($user->id)],
		])->validate();

		return $this->userRepository->edit($user, array_only($params, $user->getFillable()));
	}

	/**
	 * @param User  $user
	 * @param array $params
	 *
	 * @return bool
	 */
	protected function editCarrier(User $user, array $params)
	{
		$entityUser = array_only($params, app()->make(User::class)->getFillable());
		$entityProfile = array_only($params, app()->make(ProfileDriver::class)->getFillable());
		//$entityCar = isset($params['car']) && is_array($params['car']) ? $params['car'] : [];

		return DB::transaction(function () use ($entityUser, $entityProfile, $entityCar, $params, $user) {
			/** @var User $user */
			$this->userRepository->edit($user, $entityUser);

			if (isset($params['membership_id'])) {
				$user->memberships()->sync([$params['membership_id']]);
			}

			$this->userRepository->edit($user->profile, array_merge([
				'user_id' => $user->id
			], $entityProfile));

			if (isset($params['is_online']) && $params['is_online'] == true) {
				$this->userRepository->edit($user->profile, [
					'status' => ProfileDriver::STATUS_ONLINE
				]);
			} else {
				$this->userRepository->edit($user->profile, [
					'status' => ProfileDriver::STATUS_OFFLINE
				]);
			}

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


			foreach ([UserCar::MEDIA_CAR, UserCar::MEDIA_CAR_PLATE] as $mediaName) {
				if (isset($params['car'][$mediaName]) && $params['car'][$mediaName] instanceof UploadedFile) {
					/** @var UploadedFile $picture */
					$picture = $params['car'][$mediaName];
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
	 * @param User $user
	 */
	public function sendActivationLink(User $user)
	{
		$key = $this->makeActivationKey($user);
		\Mail::to($user)->queue(new UserActivationMail($user, $key));
	}

	/**
	 * @param User $user
	 *
	 * @return string
	 */
	protected function makeActivationKey(User $user)
	{
		return substr(sha1(json_encode([
			'id' => $user->id,
			'password' => $user->password,
			'created_at' => $user->created_at
		])), 0, 10);
	}

	/**
	 * @param User   $user
	 * @param string $key
	 *
	 * @return bool
	 */
	public function verifyKey(User $user, $key)
	{
		return ($key == $this->makeActivationKey($user));
	}

	/**
	 * @param User   $user
	 * @param string $key
	 *
	 * @return bool
	 */
	public function activateUserByKey(User $user, $key)
	{
		if (!$this->verifyKey($user, $key)) {
			return false;
		}

		$this->userRepository->edit($user->profile, ['is_activated' => true]);
		return true;
	}

	/**
	 * @param User   $user
	 * @param string $password
	 *
	 * @return bool
	 */
	public function verifyPassword(User $user, $password)
	{
		return Hash::check($password, $user->password);
	}

	/**
	 * @param User   $user
	 * @param string $password
	 */
	public function changePassword(User $user, $password)
	{
		$user->password = $password;
		$user->saveOrFail();
	}

	/**
	 * @param User $user
	 */
	public function sendConfirmationLink(User $user)
	{
		$key = $this->makeActivationKey($user);
		\Mail::to($user)->queue(new EmailConfirmationMail($user, $key));
	}

	/**
	 * @param bool $withDeleted
	 *
	 * @return Collection
	 */
	public function getAdmins($withDeleted = false)
	{
		$admins = DB::table('users')
			->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
			->rightJoin('roles', 'role_user.role_id', '=', 'roles.id')
			->where('deleted_at', null)
			->where('roles.name', '=', 'admin')
			->select([
				'users.*',
				'role_user.role_id as role_id',
				'roles.name as role_name'
			])->get();

		$adminsArray = $admins->toArray();

		foreach ($adminsArray as $idx => $props) {
			$adminsArray[$idx] = User::make((array)$props);
		}

		$collection = new Collection($adminsArray);

		return $collection;
	}

	/**
	 * @param string $role
	 *
	 * @return array
	 * @throws MultipleExceptions
	 */
	public function getNavigation($role)
	{
		switch ($role) {
			case 'carrier':
				return [
					'status' => 'success',
					'data' => [
						'data' => [
							[
								'title' => 'Profile',
								'id' => 'UserProfile',
								'href' => '/carrier/v1/user/profile',
							],
							[
								'title' => 'Trips',
								'id' => 'TripsAll',
								'href' => '/carrier/v1/trip/all',
							],
							[
								'title' => 'Orders',
								'id' => 'OrdersAll',
								'href' => '/carrier/v1/order/all',
							],
							[
								'title' => 'Payments',
								'id' => 'PaymentsAll',
								'href' => '/carrier/v1/payment/all',
							],
							[
								'title' => 'Help',
								'id' => 'InfoHelp',
								'href' => '/carrier/v1/info/help',
							],
							[
								'title' => 'About',
								'id' => 'InfoAbout',
								'href' => '/carrier/v1/info/about',
							],
							[
								'title' => 'Legal',
								'id' => 'InfoLegal',
								'href' => '/carrier/v1/info/legal',
							],
						]
					]
				];
			case 'customer':
				return [
					'status' => 'success',
					'data' => [
						'data' => [
							/*
							 * Main menu items
							 */
							[
								'title' => 'Profile',
								'id' => 'UserAccount',
								'href' => '/customer/v1/user/profile',
							],
							[
								'title' => 'Orders',
								'id' => 'OrdersAll',
								'href' => '/customer/v1/orders/all',
							],
							[
								'title' => 'Settings',
								'id' => 'ProfileSettings',
								'href' => '/customer/v1/user/profile/settings',
							],
							[
								'title' => 'Help',
								'id' => 'InfoHelp',
								'href' => '/customer/v1/info/help',
							],
							[
								'title' => 'About',
								'id' => 'InfoAbout',
								'href' => '/customer/v1/info/about',
							],
							[
								'title' => 'Legal',
								'id' => 'InfoLegal',
								'href' => '/customer/v1/info/legal',
							],
							/*
							 * Settings child items
							 */
							[
								'title' => "Payment Info",
								'id' => 'SettingsPaymentInfo',
								'href' => '/customer/v1/account/settings/payment-info',
								'parent' => 'Settings'
							],
							[
								'title' => "Notifications",
								'id' => 'SettingsNotifications',
								'href' => '/customer/v1/account/settings/notifications',
								'parent' => 'Settings'
							],
							[
								'title' => "Sign Out",
								'id' => 'SettingsSignOut',
								'href' => '/user/v1/account/sign-out',
								'parent' => 'Settings'
							],
						]
					]
				];
			case 'admin':
				return [
					'status' => 'success',
					'data' => [
						[
							'title' => 'Dashboard',
							'icon' => 'action:ic_dashboard_24px',
							'sref' => 'dashboard',
						],
						[
							'title' => 'Carriers',
							'icon' => '/img/material-design-icons/taxi.svg',
							'sref' => 'carriers',
						],
						[
							'title' => 'Customers',
							'icon' => 'social:ic_group_24px',
							'sref' => 'customers',
						],
						[
							'title' => 'Orders',
							'icon' => 'content:ic_next_week_24px',
							'sref' => 'orders',
						],
						[
							'title' => 'Trips',
							'icon' => 'maps:ic_zoom_out_map_24px',
							'sref' => 'trips',
						],
						[
							'title' => 'Shipments',
							'icon' => 'social:ic_party_mode_24px',
							'sref' => 'shipments',
						],
						[
							'title' => 'Cities',
							'icon' => 'social:ic_domain_24px',
							'sref' => 'cities',
						],
						[
							'title' => 'Administrators',
							'icon' => '/img/material-design-icons/account-settings-variant.svg',
							'sref' => 'administrators',
						],
						[
							'title' => 'Audit',
							'icon' => '/img/material-design-icons/grease-pencil.svg',
							'sref' => 'audit',
						],
						[
							'title' => 'Payments',
							'icon' => '/img/material-design-icons/cash-multiple.svg',
							'sref' => 'payments',
						],
						[
							'title' => 'Reports',
							'icon' => 'action:ic_speaker_notes_24px',
							'sref' => 'reports',
						],
						[
							'title' => 'Notifications',
							'icon' => 'communication:ic_chat_24px',
							'sref' => 'notifications',
						],
						[
							'title' => 'Settings',
							'icon' => '/img/material-design-icons/toggle-switch-off.svg',
							'sref' => 'settings',
						],
					]
				];
			default:
				throw new MultipleExceptions("Not found", 404);

		}
	}

	/**
	 * @param string $username
	 *
	 * @return array
	 */
	public function checkUsernameExistence($username)
	{
		$user = User::where('username', $username)->first();

		$data = ['result' => false];

		if (isset($user->id) && !is_null($user->id)) {
			$data['result'] = true;
		}

		return $data;

	}

	/**
	 * @param string $id
	 *
	 * @return User\
	 */
	public function getById($id)
	{
		$admin = DB::table('users')
			->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
			->rightJoin('roles', 'role_user.role_id', '=', 'roles.id')
			->where('users.id', '=', $id)
			->where('roles.name', '=', 'admin')
			->select([
				'users.*',
				'role_user.role_id as role_id',
				'roles.name as role_name'
			])->first();

		$user = User::make((array)$admin);

		return $user;
	}

	/**
	 * @param string $email
	 *
	 * @return array
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function checkEmailExistence($email)
	{

		$data = ['result' => false];

		\Validator::make(['email' => $email], [
			'email' => 'email|required'
		])->validate();

		$user = User::where('email', $email)->first();

		if (isset($user->id) && !is_null($user->id)) {
			$data['result'] = true;
		}

		return $data;

	}

	/**
	 * @param string $phone
	 *
	 * @return array
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function checkPhoneExistence($phone)
	{

		$data = ['result' => false];

		\Validator::make(['phone' => $phone], [
			'phone' => 'string|required|phone:AUTO,mobile'
		])->validate();

		$user = User::where('phone', $phone)->first();

		if (isset($user->id) && !is_null($user->id)) {
			$data['result'] = true;
		}

		return $data;

	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param string                 $id
	 *
	 * @return mixed
	 * @throws MultipleExceptions
	 */
	public function updateAdmin(EditUserProfileRequest $request, $id)
	{

		if (!$request->validate()) {
			throw new MultipleExceptions("Request validation failed", 401);
		}

		return \DB::transaction(function () use ($request, $id) {

			$user = User::find($id);

			if ($request->has('remove_image')) {
				$user->clearMediaCollection(User::PROFILE_IMAGE);
			}

			if ($request->has('name')) {
				$user->name = $request->input('name');
			}

			if ($request->has('phone')) {
				$user->phone = $request->input('phone');
			}

			if ($request->has('is_enabled')) {
				$user->is_enabled = (bool)$request->input('is_enabled') === true ? true : false;
			}

			if ($request->has(User::PROFILE_IMAGE) && $request->input(User::PROFILE_IMAGE) instanceof UploadedFile) {
				$user->clearMediaCollection(User::PROFILE_IMAGE)
					->addMediaFromRequest(User::PROFILE_IMAGE)
					->toMediaCollection(User::PROFILE_IMAGE, 's3');
			}

			$data = $request->except('_method');

			if (count($data)) {

				if (isset($data[User::PROFILE_IMAGE])) {
					unset($data[User::PROFILE_IMAGE]);
				}

				$user->fill($data);
			}

			$user->saveOrFail();

			return $user;
		});

	}

	/**
	 * Reset User password by User ID
	 *
	 * @param string $id
	 */
	public function resetPassword($id)
	{

		\Validator::make(['id' => $id], ['id' => 'required|regex:' . User::UUID_REGEX])
			->validate();

		$user = User::find($id)->first();
		$plainPass = $this->randomPassword();
		$user->password = $this->randomPassword();
		$user->saveOrFail();

		if (!is_null($user->email)){
			\Mail::to($user->email)->queue(new UserPasswordResetMail($user, $plainPass));
		}

		return ['data' => ['updated' => true]];
	}

	/**
	 * Generate random password
	 *
	 * @return string
	 */
	protected function randomPassword()
	{
		$load = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.,!+-';
		$pass = array();
		$alphaLength = strlen($load) - 1;
		for ($i = 0; $i < 8; $i++) {
			$n = mt_rand(0, $alphaLength);
			$pass[] = $load[$n];
		}
		return implode($pass);
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getUserOrders($id){
		return Order::where('customer_id', '=', $id)->get();
	}

}
