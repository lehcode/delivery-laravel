<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Shipment;

use App\Http\Requests\ShipmentRequest;
use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\ShipmentSize;
use App\Models\Trip;
use App\Repositories\Shipment\ShipmentRepository;

/**
 * Class ShipmentService
 * @package App\Services\Shipment
 */
class ShipmentService implements ShipmentServiceInterface
{
	/**
	 * @var ShipmentRepository
	 */
	protected $shipmentRepository;

	/**
	 * ShipmentService constructor.
	 *
	 * @param ShipmentRepository $shipmentRepository
	 */
	public function __construct(ShipmentRepository $shipmentRepository)
	{
		$this->shipmentRepository = $shipmentRepository;
	}

	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getCategories()
	{
		return ShipmentCategory::all();
	}

	/**
	 * @param array $data
	 *
	 * @return Shipment
	 * @throws \Exception
	 */
	public function create(array $data)
	{

		\Validator::make($data, ShipmentRequest::RULES)->validate();
		
		$result = $this->shipmentRepository->create($data);

		return $result;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getSizes()
	{
		return ShipmentSize::all();
	}

}
