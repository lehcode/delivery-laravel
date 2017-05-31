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
use App\Services\Order\OrderServiceInterface;
use App\Services\Responder\ResponderServiceInterface;

/**
 * Class OrderController
 * @package App\Http\Controllers\Customer
 */
class OrderController
{
	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var TripServiceInterface
	 */
	protected $orderService;

	/**
	 * OrderController constructor.
	 *
	 * @param ResponderServiceInterface $responderServiceInterface
	 * @param OrderServiceInterface     $orderServiceInterface
	 */
	public function __construct(
		ResponderServiceInterface $responderServiceInterface,
		OrderServiceInterface $orderServiceInterface
	) {
		$this->responderService = $responderServiceInterface;
		$this->orderService = $orderServiceInterface;
	}

	public function createOrder()
	{

	}

	public function getOrders()
	{
		return $this->responderService->fractal($this->orderService->userOrders(), OrderResponse::class);
	}
	
	public function getOrder()
	{
		//
	}
}
