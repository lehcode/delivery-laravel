<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\TripResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\SignUp\SignUpServiceInterface;
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
	 * @var SignUpServiceInterface
	 */
	protected $signupService;

	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var TripService
	 */
	protected $tripService;

	/**
	 * CarrierController constructor.
	 *
	 * @param SignUpServiceInterface    $signUpServiceInterface
	 * @param ResponderServiceInterface $responderServiceInterface
	 * @param TripService      $tripService
	 */
	public function __construct(
		SignUpServiceInterface $signUpServiceInterface,
		ResponderServiceInterface $responderServiceInterface,
		TripService $tripService
	) {
		$this->signupService = $signUpServiceInterface;
		$this->responderService = $responderServiceInterface;
		$this->tripService = $tripService;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 * @throws ValidationException
	 */
	public function create(Request $request)
	{
		try {
			$params = $request->all();
			$user = $this->signupService->carrier($params);
			return $this->responderService->fractal($user, UserDetailedResponse::class, 0, [false, true]);
		} catch (ValidationException $e) {
			throw $e;
		} catch (\Exception $e) {
			return $this->responderService->errorResponse($e);
		}

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
