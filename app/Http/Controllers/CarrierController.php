<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\SignupCarrierRequest;
use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\TripResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\Trip\TripService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * Class CarrierController
 * @package App\Http\Controllers\Carrier
 */
class CarrierController extends BaseController
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
	 * CarrierController constructor.
	 *
	 * @param SignUpService    $signUpService
	 * @param ResponderService $responderService
	 * @param TripService      $tripService
	 */
	public function __construct(
		SignUpService $signUpService,
		ResponderService $responderService,
		TripService $tripService
	) {
		$this->signupService = $signUpService;
		$this->responderService = $responderService;
		$this->tripService = $tripService;
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
		return $this->responderService->response([
			'status' => 'success',
			'data' => [
				'data' => [
					[
						'title' => 'Profile',
						'id' => 'UserProfile',
						'href' => '/carrier/v1/user/profile',
					],
					[
						'title' => 'Trips',
						'id' => 'TripsAll',
						'href' => '/carrier/v1/trip/all',
					],
					[
						'title' => 'Orders',
						'id' => 'OrdersAll',
						'href' => '/carrier/v1/order/all',
					],
					[
						'title' => 'Payments',
						'id' => 'PaymentsAll',
						'href' => '/carrier/v1/payment/all',
					],
					[
						'title' => 'Help',
						'id' => 'InfoHelp',
						'href' => '/carrier/v1/info/help',
					],
					[
						'title' => 'About',
						'id' => 'InfoAbout',
						'href' => '/carrier/v1/info/about',
					],
					[
						'title' => 'Legal',
						'id' => 'InfoLegal',
						'href' => '/carrier/v1/info/legal',
					],
				]
			]
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getUserTrips()
	{
		return $this->responderService->fractal($this->tripService->userTrips(), TripResponse::class);
	}
}
