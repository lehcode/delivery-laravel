<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers\Customer;

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
use Illuminate\Http\Request;

class CustomerController extends BaseController
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
	 * CustomerController constructor.
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
		$this->userService = $userServiceInterface;
		$this->signupService = $signUpServiceInterface;
		$this->responderService = $responderServiceInterface;
		$this->userRepository = $userRepositoryInterface;
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
			$user = $this->signupService->customer($params);
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
						'href' => '/customer/v1/user/account',
					],
					[
						'title' => 'ID Validation',
						'id' => 'IdValidation',
						'href' => '/customer/v1/user/id_validation',
					],
					[
						'title' => 'Payment Info',
						'id' => 'PaymentInfo',
						'href' => '/customer/v1/user/payment_info',
					],
					[
						'title' => 'My Orders',
						'id' => 'MyOrders',
						'href' => '/customer/v1/orders',
					],
					[
						'title' => 'Help & Legal',
						'id' => 'HelpLegal',
						'href' => '/customer/v1/help',
					],
				]
			]
		]);
	}
}
