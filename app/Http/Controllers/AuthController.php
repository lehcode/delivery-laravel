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
use Jenssegers\Date\Date;
use JWTAuth;
use App\Exceptions\MultipleExceptions;

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
		$credentials = array_filter($request->only('username', 'password'));

		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				throw new MultipleExceptions(trans('auth.failed'), 400);
			}
		} catch (\Exception $e) {
			throw new MultipleExceptions(trans('auth.failed'), 400);
		}

		$user = Auth::user();
		$user->last_login = Date::now();
		//$user->update($user->toArray());
		$user->saveOrFail();

		if (!$user->hasRole($type)) {
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

		return $this->responderService->fractal([Auth::user(), $token, false], AuthenticationTokenResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function me()
	{
		return $this->responderService->fractal(Auth::user(), UserDetailedResponse::class);
	}

	
}
