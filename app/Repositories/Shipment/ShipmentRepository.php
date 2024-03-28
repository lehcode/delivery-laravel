<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Shipment;

use App\Models\Shipment;
use App\Repositories\CrudRepository;

/**
 * Class ShipmentRepository
 * @package App\Repositories\Shipment
 */
class ShipmentRepository extends CrudRepository implements ShipmentRepositoryInterface
{
	/**
	 * @var
	 */
	protected $model = Shipment::class;

	/**
	 * @return mixed
	 */
	public function all()
	{
		return $this->model->all();
	}

	/**
	 * @param array $params
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function create(array $params)
	{
		try {
			return parent::create($params);
		} catch (\Exception $e) {
			throw $e;
		}

	}
}
