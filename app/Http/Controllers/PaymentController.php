<?php
/**
 * Created by Antony Repin
 * Date: 01.06.2017
 * Time: 9:22
 */

namespace App\Http\Controllers;

use App\Http\Requests\PaymentInfoRequest;
use App\Models\PaymentType;
use App\Services\Payment\PaymentService;
use App\Services\Responder\ResponderService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	protected $paymentService;

	protected $postData = [];

	/**
	 * PaymentController constructor.
	 *
	 * @param ResponderService $responderService
	 * @param PaymentService   $paymentService
	 * @param Request          $request
	 */
	public function __construct(
		ResponderService $responderService,
		PaymentService $paymentService,
		Request $request
	)
	{
		$this->responderService = $responderService;
		$this->paymentService = $paymentService;
		$this->postData = config('app.debug') === true ? $request->except('XDEBUG_SESSION_START') : $request->all();
	}

	/**
	 * @return mixed
	 */
	public function types()
	{
		return $this->responderService->response($this->paymentService->paymentTypes());
	}

	/**
	 * Get payment by ID
	 *
	 * @param int $id
	 */
	public function getPayment($id)
	{
		//
	}

	/**
	 * Create new payment
	 */
	public function createPayment()
	{
		//
	}

	/**
	 * Fetch User payment credentials (card #, name etc)
	 */
	public function getUserPaymentInfo()
	{
		//
	}

	/**
	 * Save User payment credentials (card #, name etc)
	 *
	 * @param PaymentInfoRequest $request
	 */
	public function storeUserPaymentInfo(PaymentInfoRequest $request)
	{
		return $this->responderService->objectResponse($this->paymentService->savePaymentInfo($this->postData));
	}
}
