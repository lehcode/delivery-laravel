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
		return ['data' => ShipmentCategory::all()];
	}

	/**
	 * @param ShipmentRequest $request
	 *
	 * @return $this
	 * @throws \Exception
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function create(ShipmentRequest $request)
	{
		\Validator::make($request->all(), $request->rules())->validate();

		$data = $request->all();
		unset($data['photosArray']);

		$shipment = $this->shipmentRepository->create($data);

		$images = $request->input('photosArray');
		if (count($images)) {
			$shipment->clearMediaCollection(Shipment::MEDIA_COLLECTION);
			foreach ($images as $img) {
				$shipment->addMedia($img)
					->usingFileName($img->hashName())
					->toMediaCollection(Shipment::MEDIA_COLLECTION, 's3');
			}
		}


		return $shipment;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getSizes()
	{
		return ['data' => ShipmentSize::all()];
	}

}
