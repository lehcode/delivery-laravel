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
 *
 * @SWG\Response(
 *     response="userDetailedResponse",
 *     description="User profile",
 *     @SWG\Schema(
 *      @SWG\Property(property="status", ref="#/definitions/textStatusProperty"),
 *      @SWG\Property(property="data", type="object", description="User data")
 *      )
 * )
 */
class UserDetailedResponse extends TransformerAbstract
{
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
					'is_enabled' => $user->is_enabled,
					'name' => $user->name,
//					'picture' => !is_null($user->profile->getFirstMedia(User::PROFILE_IMAGE))
//						? $user->profile->getFirstMedia(User::PROFILE_IMAGE)->getFullUrl('fitted')
//						: null
				];

				break;

			case User::ROLE_CARRIER:
				$profile = $user->profile;
				$data = [
					'is_enabled' => $user->is_enabled,
					'is_online' => $profile->is_online == User\Carrier::STATUS_ONLINE,
					'name' => $user->name,
					'notes' => $profile->notes,
//					'picture' => !is_null($profile->getFirstMedia(User::PROFILE_IMAGE))
//						? $profile->getFirstMedia(User::PROFILE_IMAGE)->getFullUrl('fitted')
//						: null,
				];

				if ($this->includeDetails == true) {
					$data = array_merge($data, ["notes" => $profile->notes]);
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

		if ($this->authenticateUser == true) {
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
		if (is_null($user->languages)) {
			return null;
		}

		return $this->collection($user->languages, new LanguageResponse);
	}
}
