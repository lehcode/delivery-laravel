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
				break;

			case User::ROLE_CUSTOMER:
				$current_city = $user->customer()->first()->currentCity()->with('country')->first();
				$data = [
					'current_city' => $current_city,
					'is_enabled' => false,
					'name' => $user->name,
					User::PROFILE_IMAGE => $user->photo
				];

				break;

			case User::ROLE_CARRIER:
				$profile = $user->profile;
				$data = [
					'current_city' => $user->carrier()->first()->currentCity()->first(),
					'is_enabled' => false,
					'is_online' => false,
					'name' => $user->name,
					'notes' => $profile->notes,
					User::PROFILE_IMAGE => $user->photo,
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
