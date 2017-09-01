<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 4:05
 */

namespace App\Http\Responses;

use App\Http\Responses\Admin\CarrierResponse;
use App\Models\Trip;
use App\Models\User;
use Jenssegers\Date\Date;

/**
 * Class TripResponse
 * @package App\Http\Responses
 */
class TripResponse extends ApiResponse
{

	protected $forAdmin;

	public function __construct($forAdmin = false){
		if ($forAdmin === true && \Auth::user()->hasRole(User::ADMIN_ROLES)){
			$this->forAdmin = true;
		}
	}

	/**
	 * @param Trip $trip
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function transform(Trip $trip)
	{

		$user = User::where(['id' => $trip->carrier_id])->first();

		if (!$user) {
			throw new \Exception("Carrier not found", 400);
		}

		$data = [
			'id' => $trip->id,
			'from_city' => $this->includeTransformedItem($trip->fromCity()->with('country')->first(), new CityResponse()),
			'destination_city' => $this->includeTransformedItem($trip->destinationCity()->with('country')->first(), new CityResponse()),
			'departure_date' => $trip->departure_date,
			'approx_time' => Date::createFromTimestamp($trip->approx_time * 60)->format('H:i'),
			'created_at' => $trip->created_at,
		];


		if ($this->forAdmin){
			$data['carrier'] = $this->includeTransformedItem($user->carrier, new CarrierResponse());
		} else {
			$data['carrier'] = $this->includeTransformedItem($user, new UserDetailedResponse());
		}

		return $data;
	}
}
