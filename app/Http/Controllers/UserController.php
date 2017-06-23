<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\EditUserProfileRequest;
use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\UserDetailedResponse;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\SignUp\SignUpServiceInterface;
use App\Services\UserService\UserServiceInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
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
	 * UserController constructor.
	 *
	 * @param SignUpServiceInterface    $signUpServiceInterface
	 * @param ResponderServiceInterface $responderServiceInterface
	 * @param UserServiceInterface      $userServiceInterface
	 * @param UserRepositoryInterface   $userRepositoryInterface
	 */
	public function __construct(
		SignUpServiceInterface $signUpServiceInterface,
		ResponderServiceInterface $responderServiceInterface,
		UserServiceInterface $userServiceInterface,
		UserRepositoryInterface $userRepositoryInterface
	) {
	
		$this->signupService = $signUpServiceInterface;
		$this->responderService = $responderServiceInterface;
		$this->userService = $userServiceInterface;
		$this->userRepository = $userRepositoryInterface;
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

		$data = $request->except(['XDEBUG_SESSION_START']);

		try {
			$this->userService->edit(\Auth::user(), $data);
			$user = $this->userRepository->find(\Auth::id());

			if ($request->has('old_password') && $request->has('new_password')) {
				if (!$this->userService->verifyPassword(\Auth::user(), $request->get('old_password'))) {
					throw new ModelValidationException(trans('app.common.errors.incorrect_password'));
				}
				$this->userService->changePassword(\Auth::user(), $request->get('new_password'));
			}

			return $this->responderService->fractal($user, UserDetailedResponse::class);
		} catch (ValidationException $e) {
			throw $e;
		} catch (\Exception $e) {
			return $this->responderService->errorResponse($e);
		}
	}
}
