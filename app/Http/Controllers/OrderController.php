<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 19:54
 */

namespace App\Http\Controllers;

use App\Http\Responses\OrderResponse;
use App\Services\BaseServiceInterface;
use App\Services\Order\OrderService;
use App\Services\Responder\ResponderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController
{
	/**
	 * @var ResponderService
	 */
	protected $responderService;

	/**
	 * @var OrderService
	 */
	protected $orderService;

	/**
	 * OrderController constructor.
	 *
	 * @param ResponderService $responderService
	 * @param OrderService     $orderService
	 */
	public function __construct(
		ResponderService $responderService,
		OrderService $orderService
	) {
	


		$this->responderService = $responderService;
		$this->orderService = $orderService;
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function createOrder(Request $request)
	{
		$data = $request->except('XDEBUG_SESSION_START');
		return $this->responderService->fractal($this->orderService->create($data), OrderResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function getCustomerOrders()
	{
		return $this->responderService->objectResponse($this->orderService->customerOrders());
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCarrierOrders()
	{
		return $this->responderService->objectResponse($this->orderService->carrierOrders());
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getActiveOrders()
	{
		return $this->responderService->objectResponse($this->orderService->userOrders());
	}

	/**
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function getOrder(Request $request, $id)
	{
		return $this->responderService->fractal($this->orderService->item($id), OrderResponse::class);
	}

	/**
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function updateOrder(Request $request, $id)
	{
		return $this->responderService->fractal($this->orderService->update($request, $id), OrderResponse::class);
	}
}
