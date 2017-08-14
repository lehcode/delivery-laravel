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
		return $this->responderService->response([
			'status' => 'success',
			'data' => [
				[
					'title' => 'Dashboard',
					'icon' => 'action:ic_dashboard_24px',
					'sref' => 'dashboard',
				],
				[
					'title' => 'Carriers',
					'icon' => '/img/material-design-icons/taxi.svg',
					'sref' => 'carriers',
				],
				[
					'title' => 'Customers',
					'icon' => 'social:ic_group_24px',
					'sref' => 'customers',
				],
				[
					'title' => 'Orders',
					'icon' => 'content:ic_next_week_24px',
					'sref' => 'orders',
				],
				[
					'title' => 'Trips',
					'icon' => 'maps:ic_zoom_out_map_24px',
					'sref' => 'trips',
				],
				[
					'title' => 'Shipments',
					'icon' => 'social:ic_party_mode_24px',
					'sref' => 'shipments',
				],
				[
					'title' => 'Cities',
					'icon' => 'social:ic_domain_24px',
					'sref' => 'cities',
				],
				[
					'title' => 'Administrators',
					'icon' => '/img/material-design-icons/account-settings-variant.svg',
					'sref' => 'administrators',
				],
				[
					'title' => 'Audit',
					'icon' => '/img/material-design-icons/grease-pencil.svg',
					'sref' => 'audit',
				],
				[
					'title' => 'Payments',
					'icon' => '/img/material-design-icons/cash-multiple.svg',
					'sref' => 'payments',
				],
				[
					'title' => 'Reports',
					'icon' => 'action:ic_speaker_notes_24px',
					'sref' => 'reports',
				],
				[
					'title' => 'Notifications',
					'icon' => 'communication:ic_chat_24px',
					'sref' => 'notifications',
				],
				[
					'title' => 'Settings',
					'icon' => '/img/material-design-icons/toggle-switch-off.svg',
					'sref' => 'settings',
				],
			]
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function all(){
		return $this->responderService->fractal($this->userService->getAdmins(), AdminResponse::class);
	}
}
