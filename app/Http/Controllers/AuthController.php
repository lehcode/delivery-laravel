<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:18
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Responses\AuthenticationTokenResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\UserService\UserServiceInterface;
use Illuminate\Http\Request;
use Auth;
use JWTAuth;
use App\Exceptions\MultipleExceptions;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController
{
	/**
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var UserServiceInterface
	 */
	protected $userService;

	/**
	 * AuthController constructor.
	 *
	 * @param UserRepositoryInterface   $userRepository
	 * @param ResponderServiceInterface $responderService
	 * @param UserServiceInterface      $userService
	 */
	public function __construct(
		UserRepositoryInterface $userRepository,
		ResponderServiceInterface $responderService,
		UserServiceInterface $userService
	) {
	

		$this->userRepository = $userRepository;
		$this->responderService = $responderService;
		$this->userService = $userService;
	}

	/**
	 * @param Request $request
	 * @param string  $type
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws MultipleExceptions
	 */
	public function authenticate(Request $request, $type)
	{
		$credentials = array_filter($request->only('email', 'phone', 'password'));

		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				throw new MultipleExceptions(trans('auth.failed'), 400);
			}
		} catch (\Exception $e) {
			throw new MultipleExceptions(trans('auth.failed'), 400);
		}

		if (Auth::user()->roles()->first()->name != $type) {
			throw new MultipleExceptions(trans('auth.failed'), 400);
		}

		if (!$registrationId = $request->header('X-Reg-Id')) {
			$registrationId = null;
		}

		/* @todo Add UserTokenRepository here to remember login/logout */

		$tokens = $this->userRepository->getUserDevicesToken(Auth::user(), $request->header('X-Device-Type'));

		foreach ($tokens as $regId) {
			if ($regId != $registrationId) {
				$this->userRepository->unregisterRegId($regId);
			}
		}

		return $this->responderService->fractal([Auth::user(), $token], AuthenticationTokenResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function me()
	{
		return $this->responderService->fractal(Auth::user(), UserDetailedResponse::class);
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
					[
						'title' => 'Dashboard',
						'icon' => 'action:ic_dashboard_24px',
						'sref' => '/dashboard',
					],
					[
						'title' => 'Drivers',
						'icon' => '/img/material-design-icons/taxi.svg',
						'sref' => '/drivers',
					],
					[
						'title' => 'Customers',
						'icon' => 'social:ic_group_24px',
						'sref' => '/customers',
					],
					[
						'title' => 'Orders',
						'icon' => 'content:ic_next_week_24px',
						'sref' => '/orders',
					],
					[
						'title' => 'Trips',
						'icon' => 'maps:ic_zoom_out_map_24px',
						'sref' => '/trips',
					],
					[
						'title' => 'Shipments',
						'icon' => 'social:ic_party_mode_24px',
						'sref' => '/shipments',
					],
					[
						'title' => 'Cities',
						'icon' => 'social:ic_domain_24px',
						'sref' => '/cities',
					],
					[
						'title' => 'Administrators',
						'icon' => '/img/material-design-icons/account-settings-variant.svg',
						'sref' => '/administrators',
					],
					[
						'title' => 'Audit',
						'icon' => '/img/material-design-icons/grease-pencil.svg',
						'sref' => '/audit',
					],
					[
						'title' => 'Payments',
						'icon' => '/img/material-design-icons/cash-multiple.svg',
						'sref' => '/payments',
					],
					[
						'title' => 'Reports',
						'icon' => 'action:ic_speaker_notes_24px',
						'sref' => '/reports',
					],
					[
						'title' => 'Notifications',
						'icon' => 'communication:ic_chat_24px',
						'sref' => '/notifications',
					],
					[
						'title' => 'Settings',
						'icon' => '/img/material-design-icons/toggle-switch-off.svg',
						'sref' => '/settings',
					],
				]
			]
		]);
	}
}
