<?php
/**
 * Created by Antony Repin
 * Date: 05.06.2017
 * Time: 14:48
 */

namespace App\Http\Middleware;

use App\Exceptions\MultipleExceptions;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class VerifyUuid
 * @package App\Http\Middleware
 */
class VerifyUuid
{
	/**
	 * @param Request  $request
	 * @param \Closure $next
	 *
	 * @return mixed
	 * @throws MultipleExceptions
	 */
	public function handle(Request $request, \Closure $next) {

		if ($uuid = $request->get('carrier_id')){
			if (!User::find($uuid)){
				throw new MultipleExceptions("Carrier not found", 400);
			}
		}

		if ($uuid = $request->get('customer_id')){
			if (!User::find($uuid)){
				throw new MultipleExceptions("Customer not found", 400);
			}
		}

		if ($uuid = $request->get('trip_id')){
			if (!Trip::find($uuid)){
				throw new MultipleExceptions("Trip not found", 400);
			}
		}

		if ($uuid = $request->get('order_id')){
			if (!Order::find($uuid)){
				throw new MultipleExceptions("Order not found", 400);
			}
		}

		if ($uuid = $request->get('shipment_id')){
			if (!Shipment::find($uuid)){
				throw new MultipleExceptions("Shipment not found", 400);
			}
		}

		return $next($request);

	}
}
