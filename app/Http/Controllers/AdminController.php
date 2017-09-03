<?php
/**
 * Created by Antony Repin
 * Date: 01.06.2017
 * Time: 7:32
 */

namespace App\Http\Controllers;


use App\Http\Requests\Admin\SignupAdminRequest;
use App\Http\Requests\EditUserProfileRequest;
use App\Http\Responses\Admin\AdminResponse;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\UserService\UserService;
use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class AdminController
{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var ResponderService
	 */
	protected $responderService;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * @var SignUpService
	 */
	protected $signupService;

	/**
	 * AdminController constructor.
	 *
	 * @param ResponderService $responderService
	 * @param UserService      $userService
	 * @param SignUpService    $signUpService
	 */
	public function __construct(
		ResponderService $responderService,
		UserService $userService,
		SignUpService $signUpService
	)
	{

		$this->responderService = $responderService;
		$this->userService = $userService;
		$this->signupService = $signUpService;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function navigation()
	{
		return $this->responderService->response($this->userService->getNavigation('admin'));
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function all()
	{
		return $this->responderService->fractal($this->userService->getAdmins(), AdminResponse::class);
	}

	/**
	 * @param string $uuid
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function get(Request $request, $uuid)
	{
		return $this->responderService->fractal($this->userService->getById($uuid), AdminResponse::class);
	}

	/**
	 * @param SignupAdminRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function create(SignupAdminRequest $request)
	{
		return $this->responderService->fractal($this->signupService->admin($request), AdminResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $username
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function checkAdminExistence(Request $request, $username)
	{
		return $this->responderService->response($this->userService->checkExistence($username));
	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param string                 $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function update(EditUserProfileRequest $request, $id)
	{
		return $this->responderService->fractal($this->userService->updateAdmin($request, $id), AdminResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function resetPassword(Request $request, $id){
		return $this->responderService->response($this->userService->resetPassword($id));
	}
}
