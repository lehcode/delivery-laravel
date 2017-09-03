<?php
/**
 * Created by Antony Repin
 * Date: 15.05.2017
 * Time: 7:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\EditUserProfileRequest;
use App\Http\Requests\PaymentInfoRequest;
use App\Http\Requests\PaymentInfoUpdateRequest;
use App\Http\Requests\SignupCustomerRequest;
use App\Http\Responses\Admin\CustomerResponse;
use App\Http\Responses\PaymentInfoResponse;
use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\UserDetailedResponse;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Customer\CustomerService;
use App\Services\Payment\PaymentService;
use App\Services\Responder\ResponderService;
use App\Services\SignUp\SignUpService;
use App\Services\Trip\TripService;
use App\Services\UserService\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * Class CustomerController
 * @package App\Http\Controllers\Customer
 */
class CustomerController extends Controller
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * @var SignUpService
	 */
	protected $signupService;

	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var CustomerService
	 */
	protected $customerService;

	/**
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * @var TripServiceInterface
	 */
	protected $tripService;

	/**
	 * @var PaymentService
	 */
	protected $paymentService;

	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * CustomerController constructor.
	 *
	 * @param SignUpService    $signUpService
	 * @param ResponderService $responderService
	 * @param CustomerService  $customerService
	 * @param TripService      $tripService
	 * @param PaymentService   $paymentService
	 * @param UserService      $userService
	 */
	public function __construct(
		SignUpService $signUpService,
		ResponderService $responderService,
		CustomerService $customerService,
		TripService $tripService,
		PaymentService $paymentService,
		UserService $userService
	)
	{
		$this->signupService = $signUpService;
		$this->responderService = $responderService;
		$this->customerService = $customerService;
		$this->tripService = $tripService;
		$this->paymentService = $paymentService;
		$this->userService = $userService;
	}

	/**
	 * @param SignupCustomerRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function create(SignupCustomerRequest $request)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->signupService->customer($request),  UserDetailedResponse::class, null, [false, true]);

	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param string                 $user_id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(EditUserProfileRequest $request, $user_id = null)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->update($request, $user_id), UserDetailedResponse::class, 0, [false]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \App\Exceptions\MultipleExceptions
	 * @throws \Exception
	 */
	public function navigation()
	{
		if (!\Auth::user()->hasRole(['customer'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->response($this->userService->getNavigation('customer'));
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function getTrips()
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->tripService->all(), TripDetailsResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function getPaymentInfo()
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal(\Auth::user()->customer, PaymentInfoResponse::class);
	}

	/**
	 * @param PaymentInfoRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function storePaymentInfo(PaymentInfoRequest $request)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->savePaymentInfo($request), PaymentInfoResponse::class);
	}

	/**
	 * @param PaymentInfoUpdateRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function updatePaymentInfo(PaymentInfoUpdateRequest $request)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->savePaymentInfo($request), PaymentInfoResponse::class);
	}

	/**
	 * Fetch list of all Customers accounts
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function all()
	{
		if (!\Auth::user()->hasRole(User::ADMIN_ROLES)) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->all(), CustomerResponse::class, 0, [$this->userService]);
	}

	/**
	 * @param Request $request
	 * @param string  $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedException
	 * @throws \Exception
	 */
	public function get(Request $request, $id)
	{
		if (!\Auth::user()->hasRole(['admin', 'customer'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->byId($request, $id), UserDetailedResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $user_id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function setAccountStatus(Request $request, $user_id = null)
	{
		if (!\Auth::user()->hasRole(['admin', 'customer'])) {
			throw new AccessDeniedException();
		}

		return $this->responderService->fractal($this->customerService->setAccountStatus($request, $user_id), UserDetailedResponse::class);

	}
}
