<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\SignupCustomerRequest;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\Trip\TripService;
use App\Services\UserService\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class CustomerController
 * @package App\Http\Controllers\Customer
 */
class CustomerController extends Controller
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var SignUpService
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
	 * @param SignUpService    $signUpService
	 * @param ResponderService $responderService
	 * @param UserService      $userService
	 * @param TripService      $tripService
	 */
	public function __construct(
		SignUpService $signUpService,
		ResponderService $responderService,
		UserService $userService,
		TripService $tripService
	)
	{
		$this->signupService = $signUpService;
		$this->responderService = $responderService;
		$this->userService = $userService;
		$this->tripService = $tripService;
	}

	/**
	 * @param SignupCustomerRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function create(SignupCustomerRequest $request)
	{
		return $this->responderService->fractal($this->signupService->customer($request), UserDetailedResponse::class);

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
