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
use App\Services\UserService\UserService;

/**
 * Class OrderService
 * @package App\Services\Order
 */
class OrderService implements OrderServiceInterface
{

	/**
	 * @var OrderRepository
	 */
	protected $orderRepository;

	/**
	 * OrderService constructor.
	 *
	 * @param OrderRepository $orderRepository
	 */
	public function __construct(OrderRepository $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}

	/**
	 * @param array $data
	 */
	public function create(array $data)
	{
		$this->orderRepository->create($data);
	}

	/**
	 * @param Order $order
	 * @param array $data
	 */
	public function edit(Order $order, array $data)
	{
		// TODO: Implement edit() method.
	}

	/**
	 * @return mixed
	 */
	public function all()
	{
		return $this->orderRepository->all();
	}

	/**
	 * @param $id
	 */
	public function item($id)
	{
		\Validator::make(['id' => $id], [
			'id' => 'required|regex:/' . UserService::UUID_REGEX . '/',
		])->validate();

		return  $this->orderRepository->find($id);
		
	}

	/**
	 * @return mixed
	 */
	public function customerOrders()
	{
		$orders = $this->orderRepository->customerOrders();

		return $orders;
	}

	/**
	 * @return mixed
	 */
	public function carrierOrders()
	{
		return $this->orderRepository->carrierOrders();
	}

}
