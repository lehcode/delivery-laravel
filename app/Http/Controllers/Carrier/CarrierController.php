<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers\Carrier;

use App\Http\Requests\SignUpCustomerRequest;
use App\Http\Responses\UserDetailedResponse;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\SignUp\SignUpServiceInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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
	 * UserController constructor.
	 *
	 * @param SignUpServiceInterface    $signUpServiceInterface
	 * @param ResponderServiceInterface $responderServiceInterface
	 */
	public function __construct(
		SignUpServiceInterface $signUpServiceInterface,
		ResponderServiceInterface $responderServiceInterface
	) {
	
		$this->signupService = $signUpServiceInterface;
		$this->responderService = $responderServiceInterface;
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
						'title' => 'My Account',
						'id' => 'MyAccount',
						'href' => '/carrier/v1/user/account',
					],
					[
						'title' => 'ID Validation',
						'id' => 'IdValidation',
						'href' => '/carrier/v1/user/id_validation',
					],
					[
						'title' => 'Payment Info',
						'id' => 'PaymentInfo',
						'href' => '/carrier/v1/user/payment_info',
					],
					[
						'title' => 'My Trips',
						'id' => 'MyOrders',
						'href' => '/carrier/v1/trips',
					],
					[
						'title' => 'Help & Legal',
						'id' => 'HelpLegal',
						'href' => '/carrier/v1/help',
					],
				]
			]
		]);
	}
}
