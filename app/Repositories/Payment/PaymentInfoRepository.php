<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Payment;

use App\Models\Trip;
use App\Repositories\CrudRepository;
use App\Repositories\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class TripRepository
 * @package App\Repositories\Trip
 */
class PaymentInfoRepository extends CrudRepository implements PaymentInfoRepositoryInterface
{
	/**
	 * @var
	 */
	protected $model = User::class;

	


}
