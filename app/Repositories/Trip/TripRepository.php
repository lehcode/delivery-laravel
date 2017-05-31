<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Trip;

use App\Models\Trip;
use App\Repositories\CrudRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class TripRepository
 * @package App\Repositories\Trip
 */
class TripRepository extends CrudRepository implements TripRepositoryInterface
{
	/**
	 * @var
	 */
	protected $model = Trip::class;

	/**
	 * @return mixed
	 */
	public function all()
	{
		return Trip::all();
	}

	public function userTrips()
	{
		$user = Auth::getUser();
		$result = Trip::where('carrier_id', $user->id);
		return $result;
	}
}
