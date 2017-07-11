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
class UserDetailedResponse extends ApiResponse
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

		$roles = $user->roles();
		$role = $roles->first();

		switch ($role->name) {
			case User::ROLE_ADMIN:
				break;

			case User::ROLE_CUSTOMER:
				$location = $user->customer->currentCity()->with('country')->first();
				$city = $location->toArray();
				$city['country'] = $location->country->toArray();

				$data = [
					'is_enabled' => $user->is_enabled,
					'current_city' => $city,
					'name' => $user->name,
					User::PROFILE_IMAGE => !is_null($user->photo) ? $user->getMedia(User::PROFILE_IMAGE)->first()->getUrl('thumb') : '',
					'payment_info' => $this->includeTransformedItem($user->customer, new PaymentInfoResponse()),
				];

				if ($this->includeDetails == true) {
					$data = array_merge($data, ["notes" => $user->customer->notes]);
				}

				break;

			case User::ROLE_CARRIER:
				$location = $user->carrier->currentCity()->with('country')->first();
				$city = $location->toArray();
				$city['country'] = $location->country->toArray();

				$data = [
					'is_enabled' => $user->is_enabled,
					'current_city' => $city,
					'is_online' => $user->carrier->is_online,
					'name' => $user->name,
					User::PROFILE_IMAGE => !is_null($user->photo) ? $user->getMedia(User::PROFILE_IMAGE)->first()->getUrl('thumb') : '',
				];

				if ($this->includeDetails == true) {
					$data = array_merge($data, ["notes" => $user->carrier->notes]);
				}
				break;
		}

		$data = array_merge($data, [
			'id' => $user->id,
			'name' => $user->name,
			'email' => $user->email,
			'phone' => $user->phone,
			'is_enabled' => $user->is_enabled,
			'created_at' => $user->created_at->format('r'),
			//'updated_at' => $user->updated_at,
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
