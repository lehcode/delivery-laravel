<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\EditUserProfileRequest;
use App\Http\Requests\SignupCarrierRequest;
use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\Admin\CarrierResponse;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\TripResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Models\User;
use App\Services\Carrier\CarrierService;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\Trip\TripService;
use App\Services\UserService\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class CarrierController
 * @package App\Http\Controllers\Carrier
 */
class CarrierController
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
	 * @var TripService
	 */
	protected $tripService;

	/**
	 * @var CarrierService
	 */
	protected $carrierService;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * CarrierController constructor.
	 *
	 * @param SignUpService    $signUpService
	 * @param ResponderService $responderService
	 * @param TripService      $tripService
	 * @param CarrierService   $carrierService
	 * @param UserService      $userService
	 */
	public function __construct(
		SignUpService $signUpService,
		ResponderService $responderService,
		TripService $tripService,
		CarrierService $carrierService,
		UserService $userService
	)
	{
		$this->signupService = $signUpService;
		$this->responderService = $responderService;
		$this->tripService = $tripService;
		$this->carrierService = $carrierService;
		$this->userService = $userService;
	}

	/**
	 * @param SignupCarrierRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function create(SignupCarrierRequest $request)
	{
		return $this->responderService->fractal($this->signupService->carrier($request), UserDetailedResponse::class, 0, [false, true]);

	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function navigation()
	{
		if (!\Auth::user()->hasRole('carrier')) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->response($this->userService->getNavigation('carrier'));
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getUserTrips()
	{
		if (!\Auth::user()->hasRole('carrier')) {
			throw new AccessDeniedHttpException("Forbidden");
		}
		
		return $this->responderService->fractal($this->tripService->userTrips(), TripResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string $orderBy
	 * @param string $order
	 * 
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException|\Exception
	 */
	public function all(Request $request, $orderBy='created_at', $order='asc')
	{

		if (!\Auth::user()->hasRole(User::ADMIN_ROLES)) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->carrierService->all($orderBy, $order), CarrierResponse::class);
	}

	/**
	 * @params Request $request
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function get(Request $request, $id)
	{
		return $this->responderService->fractal($this->carrierService->byId($request, $id), CarrierResponse::class);
	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param int                    $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function edit(EditUserProfileRequest $request, $id)
	{
		return $this->responderService->fractal($this->carrierService->update($request, $id), CarrierResponse::class);
	}
}
