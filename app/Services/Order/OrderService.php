<?php

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:34
 */
namespace App\Services\Order;

use App\Http\Requests\RecipientRequest;
use App\Models\Order;
use App\Models\Recipient;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use App\Services\Builder;
use App\Services\Trip;
use App\Services\UserService\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
	 * @param Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function create(Request $request)
	{
		$data = $request->except('XDEBUG_SESSION_START');
		$result = $this->orderRepository->create($data);

		return $result;
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
			'id' => 'required|regex:/' . User::UUID_REGEX . '/',
		])->validate();

		return $this->orderRepository->find($id);

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

	/**
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return mixed
	 */
	public function update(Request $request, $id)
	{
		$data = $request->except(['XDEBUG_SESSION_START']);

		if (isset($data['status'])) {
			return $this->orderRepository->updateStatus($id, $data['status']);
		}


	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getByShipmentType($id)
	{
		try {
			$result = Order::where('customer_id', '=', \Auth::user()->id)
				->with('trip', 'recipient', 'shipment', 'customer')
				->get()
				->filter(function ($order) use ($id) {
					if ($order->shipment->category_id === $id) {
						$order->load('trip.paymentType')->get();
						return $order;
					}
				});
		} catch (\Exception $e) {
			throw $e;
		}


		return $result;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function createRecipient(Request $request)
	{
		$data = $request->except('XDEBUG_SESSION_START');

		\Validator::make($data, RecipientRequest::RULES)->validate();

		$result = factory(Recipient::class)->create($data);

		return $result;
	}

}
