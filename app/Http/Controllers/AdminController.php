<?php
/**
 * Created by Antony Repin
 * Date: 01.06.2017
 * Time: 7:32
 */

namespace App\Http\Controllers;


use App\Http\Requests\Admin\SignupAdminRequest;
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
	) {
	
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
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function get($id)
	{
		return $this->responderService->fractal($this->userService->get($id), AdminResponse::class);
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
	public function checkUsernameExistence(Request $request, $username)
	{
		return $this->responderService->response($this->userService->checkExistence($username));
	}
}
