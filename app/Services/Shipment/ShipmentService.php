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
	 * @param ShipmentRequest $request
	 *
	 * @return $this
	 * @throws \Exception
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function create(ShipmentRequest $request)
	{

		$data = $request->except('XDEBUG_SESSION_START');
		\Validator::make($data, $request->rules())->validate();

		foreach ($request->input('photosArray') as $idx => $file) {
			try {
				$path = \Storage::disk('s3')->putFile('shipments', $file);
				\Storage::disk('s3')->setVisibility($path, 'public');
			} catch (\Exception $e) {
				throw $e;
			}

			$data['image_url'][] = $path;
		}
		
		unset($data['photosArray']);

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
