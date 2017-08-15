<?php
/**
 * Created by Antony Repin
 * Date: 01.06.2017
 * Time: 7:32
 */

namespace App\Http\Controllers;


use App\Http\Responses\Admin\AdminResponse;
use App\Repositories\User\UserRepository;
use App\Services\Responder\ResponderService;
use App\Services\UserService\UserService;
use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AdminController
{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * @var ResponderService
	 */
	protected $responderService;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * AdminController constructor.
	 *
	 * @param UserRepository   $userRepository
	 * @param ResponderService $responderService
	 * @param UserService      $userService
	 */
	public function __construct(
		UserRepository $userRepository,
		ResponderService $responderService,
		UserService $userService
	) {
	
		$this->userRepository = $userRepository;
		$this->responderService = $responderService;
		$this->userService = $userService;
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
	public function all(){
		return $this->responderService->fractal($this->userService->getAdmins(), AdminResponse::class);
	}
}
