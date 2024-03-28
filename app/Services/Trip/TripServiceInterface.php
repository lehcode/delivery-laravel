<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 10:35
 */

namespace App\Services\Trip;

use App\Http\Requests\TripRequest;
use App\Models\Trip;
use App\Services\CrudServiceInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TripServiceInterface
 * @package App\Services\Trip
 */
interface TripServiceInterface extends CrudServiceInterface
{
	/**
	 * @param TripRequest $data
	 *
	 * @return Trip
	 */
	public function create(TripRequest $data);

	/**
	 * Get all Trips ordered by created_at ASC
	 *
	 * @param string $orderBy
	 * @param string $order
	 *
	 * @return Collection
	 */
	public function all($orderBy, $order);

	/**
	 * @return Collection
	 */
	public function getTripsFromCurrentCity();
}
