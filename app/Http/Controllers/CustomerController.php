<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\SignUp\SignUpServiceInterface;
use App\Services\Trip\TripServiceInterface;
use App\Services\UserService\UserServiceInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * Class CustomerController
 * @package App\Http\Controllers\Customer
 */
class CustomerController extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var SignUpServiceInterface
	 */
	protected $signupService;

	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var UserServiceInterface
	 */
	protected $userService;

	/**
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * @var TripServiceInterface
	 */
	protected $tripService;

	/**
	 * CustomerController constructor.
	 *
	 * @param SignUpServiceInterface    $signUpServiceInterface
	 * @param ResponderServiceInterface $responderServiceInterface
	 * @param UserServiceInterface      $userServiceInterface
	 * @param UserRepositoryInterface   $userRepositoryInterface
	 * @param TripServiceInterface      $tripServiceInterface
	 */
	public function __construct(
		SignUpServiceInterface $signUpServiceInterface,
		ResponderServiceInterface $responderServiceInterface,
		UserServiceInterface $userServiceInterface,
		UserRepositoryInterface $userRepositoryInterface,
		TripServiceInterface $tripServiceInterface
	) {
	
		$this->userService = $userServiceInterface;
		$this->signupService = $signUpServiceInterface;
		$this->responderService = $responderServiceInterface;
		$this->userRepository = $userRepositoryInterface;
		$this->tripService = $tripServiceInterface;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 * @throws ValidationException
	 */
	public function create(Request $request)
	{
		try {
			$params = $request->all();
			$user = $this->signupService->customer($params);
			return $this->responderService->fractal($user, UserDetailedResponse::class, 0, [false, true]);
		} catch (ValidationException $e) {
			throw $e;
		} catch (\Exception $e) {
			return $this->responderService->errorResponse($e);
		}

	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function navigation()
	{

		return $this->responderService->response([
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
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTrips()
	{
		return $this->responderService->fractal($this->tripService->all(), TripDetailsResponse::class);
	}
}
