<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Exceptions\MultipleExceptions;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Responses\UserDetailedResponse;
use App\Repositories\User\UserRepository;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\UserService\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var SignUpService
	 */
	protected $signupService;

	/**
	 * @var ResponderService
	 */
	protected $responderService;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * UserController constructor.
	 *
	 * @param SignUpService    $signUpService
	 * @param ResponderService $responderService
	 * @param UserService      $userService
	 * @param UserRepository   $userRepository
	 */
	public function __construct(
		SignUpService $signUpService,
		ResponderService $responderService,
		UserService $userService,
		UserRepository $userRepository
	) {
	
		$this->signupService = $signUpService;
		$this->responderService = $responderService;
		$this->userService = $userService;
		$this->userRepository = $userRepository;
	}

	/**
	 * @param EditUserProfileRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ModelValidationException
	 * @throws ValidationException
	 */
	public function edit(EditUserProfileRequest $request)
	{
		$this->userService->edit(\Auth::user(), $request->all());
		$user = $this->userRepository->find(\Auth::id());

		if ($request->has('old_password') && $request->has('new_password')) {
			if (!$this->userService->verifyPassword(\Auth::user(), $request->get('old_password'))) {
				throw new ModelValidationException(trans('app.common.errors.incorrect_password'));
			}
			$this->userService->changePassword(\Auth::user(), $request->get('new_password'));
		}

		return $this->responderService->fractal($user, UserDetailedResponse::class);
	}

	/**
	 * @param $userId
	 * @param $key
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function verify($userId, $key) {
		try {
			/** @var User $user */
			if(!$user = $this->userRepository->find($userId)) {
				throw new MultipleExceptions(trans('app.common.errors.user_not_found'));
			}

			if($this->userService->verifyKey($user, $key)) {
				$this->userService->activateUserByKey($user, $key);
			} else {
				throw new MultipleExceptions(trans('app.common.errors.cannot_activate'));
			}

			return \View::make('activate');
		} catch(MultipleExceptions $e) {
			return \View::make('activate')->withErrors($e->getMessages());
		}
	}

	/**
	 * @param Request $request
	 * @param string  $username
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function checkUsernameExistence(Request $request, $username)
	{
		return $this->responderService->response($this->userService->checkUsernameExistence($username));
	}

	/**
	 * @param Request $request
	 * @param string  $email
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function checkEmailExistence(Request $request, $email)
	{
		return $this->responderService->response($this->userService->checkEmailExistence($email));
	}

	/**
	 * @param Request $request
	 * @param         $phone
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function checkPhoneExistence(Request $request, $phone)
	{
		return $this->responderService->response($this->userService->checkPhoneExistence($phone));
	}

	/**
	 *
	 */
	public function resetPassword(){

	}


}
