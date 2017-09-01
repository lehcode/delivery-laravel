<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Trip;

use App\Models\Trip;
use App\Repositories\CrudRepository;
use Illuminate\Support\Collection;

/**
 * Class TripRepository
 * @package App\Repositories\Trip
 */
class TripRepository extends CrudRepository implements TripRepositoryInterface
{
	/**
	 * @var string
	 */
	protected $model = Trip::class;


	/**
	 * @param string $orderBy
	 * @param string $order
	 *
	 * @return Collection
	 */
	public function all($orderBy='created_at', $order='desc')
	{

		if ($order === 'asc'){
			return Trip::all()
				->sortBy($orderBy);
		}

		return Trip::all()
			->sortByDesc($orderBy);
	}

	/**
	 * @return Collection
	 */
	public function userTrips()
	{
		return Trip::where('carrier_id', \Auth::user()->id)
			->get();
	}

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function find($id)
	{
		return parent::find($id);
	}
}
