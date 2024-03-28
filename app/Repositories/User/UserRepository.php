<?php

/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 3:11
 */

namespace App\Repositories\User;

use App\Models\User;
use App\Models\UserDevice;
use App\Repositories\CrudRepository;
use Cache;

/**
 * Class UserRepository
 * @package App\Repositories\User
 */
class UserRepository extends CrudRepository implements UserRepositoryInterface
{
	protected $model = User::class;

	/**
	 * @param User $user
	 * @param $type
	 * @return array
	 */
	public function getUserDevicesToken(User $user, $type)
	{
		if (is_null($type)) {
			return array_filter(UserDevice::where('id', $user->id)->pluck('reg_id')->toArray());
		}

		return array_filter(UserDevice::where('type', $type)->where('id', $user->id)->pluck('reg_id')->toArray());
	}

	/**
	 * @param User $user
	 * @return array
	 */
	public function getUserDevicesTypes(User $user)
	{
		return Cache::tags(["user{id:{$user->id}}"])->remember("deviceTypes{user:{$user->id}}", 60, function () use ($user) {
			return array_unique(UserDevice::where('id', $user->id)->pluck('type')->toArray());
		});
	}

	/**
	 * @param string $regId
	 * @return bool
	 */
	public function unregisterRegId($regId)
	{
		return UserDevice::where('reg_id', $regId)->delete();
	}
	
}
