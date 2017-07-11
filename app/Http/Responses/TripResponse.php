<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 4:05
 */

namespace App\Http\Responses;

use App\Models\Trip;
use App\Models\User;
use Jenssegers\Date\Date;

/**
 * Class TripResponse
 * @package App\Http\Responses
 */
class TripResponse extends ApiResponse
{
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
			throw new \Exception("Carrier not found", 404);
		}

		$data = [
			'id' => $trip->id,
			'payment_type' => $trip->paymentType()->first(),
			'from_city' => $this->includeTransformedItem($trip->fromCity()->with('country')->first(), new CityResponse()),
			'destination_city' => $this->includeTransformedItem($trip->destinationCity()->with('country')->first(), new CityResponse()),
			'carrier' => $this->includeTransformedItem($user, new UserDetailedResponse()),
			'departure_date' => $trip->departure_date,
			'approx_time' => Date::createFromTimestamp($trip->approx_time * 60)->format('H:i'),
			'created_at' => $trip->created_at,
		];

		return $data;
	}
}
