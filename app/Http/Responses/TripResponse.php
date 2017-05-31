<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 4:05
 */

namespace App\Http\Responses;

use App\Models\Trip;
use App\Models\User;

class TripResponse extends ApiResponse
{
	protected $showDetails;

	protected $showAdminDetails;

	public function __construct($showDetails = true, $showAdminDetails = false)
	{
		$this->showDetails = $showDetails;
		$this->showAdminDetails = $showAdminDetails;
	}

	public function transform(Trip $trip)
	{

		$user = User::where(['id' => $trip->carrier_id])->first();
		$carrier = $user->carrier()->with('currentCity')->first();
		$carrier->makeHidden('current_city');
		$paymentType = $trip->paymentType()->first();

		$data = [
			'id' => $trip->id,
			'payment_type' => $paymentType,
			'carrier' => $carrier,
			'created_at' => $trip->created_at,
			'updated_at' => $trip->updated_at,
		];

		return $data;
	}
}
