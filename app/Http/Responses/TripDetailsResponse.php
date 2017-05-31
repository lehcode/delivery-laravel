<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:11
 */

namespace App\Http\Responses;

use League\Fractal\TransformerAbstract;
use App\Models\Trip;

/**
 * Class TripDetailsResponse
 * @package App\Http\Responses
 */
class TripDetailsResponse extends TransformerAbstract
{
	/**
	 * @param TripDetail $tripDetail
	 *
	 * @return array
	 */
	public function transform(Trip $trip)
	{
		return [
			'payment_type' => $trip->payment_type->id,
//			'destination_id' => $tripDetail->destination_id,
//			'destination' => !is_null($tripDetail->destination) ? $this->includeTransformedItem($tripDetail->destination, new DestinationResponse()) : null,
//			'duration_mins' => $tripDetail->duration_mins,
//			'is_enabled' => $tripDetail->is_enabled
		];
	}
}
