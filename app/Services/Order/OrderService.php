<?php

/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:34
 */

namespace App\Services\Order;

use App\Http\Requests\Admin\OrderRequest as AdminOrderRequest;
use App\Http\Requests\RecipientRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Recipient;
use App\Models\Shipment;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Shipment\ShipmentRepository;
use App\Services\Builder;
use App\Services\Trip;
use App\Services\UserService\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

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
	 * @var UserService
	 */
	protected $userService;

	/**
	 * @var ShipmentRepository
	 */
	protected $shipmentRepository;

	/**
	 * OrderService constructor.
	 *
	 * @param OrderRepository    $orderRepository
	 * @param UserService        $userService
	 * @param ShipmentRepository $shipmentRepository
	 */
	public function __construct(OrderRepository $orderRepository,
	                            UserService $userService,
	                            ShipmentRepository $shipmentRepository)
	{
		$this->orderRepository = $orderRepository;
		$this->userService = $userService;
		$this->shipmentRepository = $shipmentRepository;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function create(Request $request)
	{
		$result = $this->orderRepository->create($request->all());

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
		$data = $request->all();
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
		$data = $request->all();
		\Validator::make($data, RecipientRequest::RULES)->validate();
		$result = factory(Recipient::class)->create($data);

		return $result;
	}

	/**
	 * @param AdminOrderRequest $request
	 *
	 * @return Order
	 */
	public function createAdminOrder(AdminOrderRequest $request)
	{
		$orderData = [];

		$orderData['geo_start'] = $request->input('start_coord');
		$orderData['geo_end'] = $request->input('end_coord');
		$orderData['customer_id'] = $this->userService->getByUsername($request->input('customer'))->id;

		$recipientEntity = factory(Recipient::class)->create([
			'name' => $request->input('recipient_name'),
			'phone' => $request->input('recipient_phone'),
			'notes' => $request->input('recipient_notes'),
		]);
		$recipientEntity->saveOrFail();

		$orderData['recipient_id'] = $recipientEntity->id;

		$shipment['size_id'] = (int) $request->input('shipment_size');
		$shipment['category_id'] = (int) $request->input('shipment_category');

		$shipmentEntity = $this->shipmentRepository->create($shipment);
		$shipmentEntity->saveOrFail();

		$orderData['shipment_id'] = $shipmentEntity->id;

		if ($request->has('images')) {
			$shipmentEntity->clearMediaCollection(Shipment::MEDIA_COLLECTION);
			foreach ($request->input('images') as $img) {
				$shipmentEntity->addMedia($img)
					->usingFileName($img->hashName())
					->toMediaCollection(Shipment::MEDIA_COLLECTION, 's3');
			}
		}

		$payment['id'] = Uuid::generate(4)->string;
		$payment['status'] = Payment::STATUS_UNPAID;
		$paymentEntity = new Payment();
		$paymentEntity->saveOrFail($payment);

		$orderData['price'] = (float)$request->input('price');
		$orderData['departure_date'] = null;
		$orderData['expected_delivery_date'] = (float)$request->input('expected_delivery_date');

		$orderEntity = factory(Order::class)->create($orderData);
		$orderEntity->saveOrFail();

		return $orderEntity;

	}

}
