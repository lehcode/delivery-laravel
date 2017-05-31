<?php

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:34
 */
namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Builder;
use App\Services\Trip;

class OrderService implements OrderServiceInterface
{

	protected $orderRepository;

	public function __construct(OrderRepository $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}

	public function create(array $data)
	{
		// TODO: Implement create() method.
	}

	public function edit(Order $order, array $data)
	{
		// TODO: Implement edit() method.
	}

	public function all()
	{
		return $this->orderRepository->all();
	}

	public function item($id)
	{
		// TODO: Implement item() method.
	}
	
	public function userOrders(){
		return $this->orderRepository->userOrders();
	}

}
