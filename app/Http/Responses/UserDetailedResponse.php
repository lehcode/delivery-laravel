<?php
	/**
	 * Created by Antony Repin
	 * Date: 24.04.2017
	 * Time: 18:25
	 */

namespace App\Http\Responses;

use App\Models\ProfileCustomer;
use App\Models\ProfileDriver;
use App\Models\User;
use App\Models\UserCar;
use App\Services\Rating\RatingServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;
use JWTAuth;

	/**
	* Class UserDetailedResponse
	* @package App\Http\Responses
	*/
class UserDetailedResponse extends TransformerAbstract
{
	/**
	 * @var array
	 */
	protected $defaultIncludes = [
		'language'
	];

	/**
	 * @var bool
	 */
	protected $includeDetails;

	/**
	 * @var bool
	 */
	protected $authenticateUser;

	/**
	 * UserDetailedResponse constructor.
	 *
	 * @param bool $includeDetails
	 * @param bool $authenticateUser
	 */
	public function __construct($includeDetails = false, $authenticateUser = false)
	{

		$this->includeDetails = $includeDetails;
		$this->authenticateUser = $authenticateUser;
	}

	/**
	 * @param User $user
	 *
	 * @return array
	 */
	public function transform(User $user)
	{
		/* @var RatingServiceInterface $ratingService */
		//$ratingService = app()->make(RatingServiceInterface::class);

		$data = [];
		$roles = $user->roles();
		$role = $roles->first();

		switch ($role->name) {
			case User::ROLE_ADMIN:
				$data = [];
				break;

			case User::ROLE_CUSTOMER:
				$data = [
					'name' => $user->profile->name,
					'is_activated' => $user->profile->is_activated,
					'picture' => !is_null($user->profile->getFirstMedia(User\Customer::MEDIA_PICTURE))
						? $user->profile->getFirstMedia(User\Customer::MEDIA_PICTURE)->getFullUrl('fitted')
						: null
				];

				break;

			case User::ROLE_CARRIER:
				$data = [
					'is_online' => $user->profile->status == ProfileDriver::STATUS_ONLINE,
					'name' => $user->profile->name,
					'status' => $user->profile->status,
					'notes' => $user->profile->notes,
					'picture' => !is_null($user->profile->getFirstMedia(ProfileDriver::MEDIA_PICTURE))
						? $user->profile->getFirstMedia(ProfileDriver::MEDIA_PICTURE)->getFullUrl('fitted')
						: null,
				];

				if ($this->includeDetails == true)
				{
					$data = array_merge($data, [ "notes" => $user->profile->notes ]);
				}
				break;
		}

		$data = array_merge($data, [
			'id' => $user->id,
			'name' => $user->name,
			'email' => $user->email,
			'phone' => $user->phone,
			'is_enabled' => $user->is_enabled,
			'created_at' => $user->created_at,
			'updated_at' => $user->updated_at,
			'roles' => $user->roles,
		]);

		if ($this->authenticateUser == true)
		{
			$token = JWTAuth::fromUser($user);
			$data = array_merge($data, ['token' => $token]);
		}

		return $data;
	}

	/**
	 * @param User $user
	 *
	 * @return \League\Fractal\Resource\Collection|null
	 */
	public function includeLanguage(User $user)
	{
		if (is_null($user->languages))
		{
			return null;
		}

		return $this->collection($user->languages, new LanguageResponse);
	}
}
