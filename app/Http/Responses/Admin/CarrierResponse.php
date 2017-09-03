<?php
/**
 * Created by Antony Repin
 * Date: 8/3/2017
 * Time: 00:28
 */

namespace App\Http\Responses\Admin;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\CityResponse;
use App\Models\User;
use App\Models\User\Carrier;

/**
 * Class CarrierResponse
 * @package App\Http\Responses\Admin
 */
class CarrierResponse extends ApiResponse
{
	/**
	 * @param Carrier $carrier
	 *
	 * @return array
	 */
	public function transform(Carrier $carrier)
	{

		$carrier->load(['user']);

		$data = [
			'id' => $carrier->id,
			"username" => $carrier->user->username,
			"phone" => $carrier->user->phone,
			"email" => $carrier->user->email,
			"name" => !is_null($carrier->user->name) ? $carrier->user->name : "",
			'is_enabled' => $carrier->user->is_enabled,
			"is_online" => $carrier->is_online,
			"notes" => !is_null($carrier->notes) ? $carrier->notes : "",
			"cash_payments" => 0,
			"card_payments" => 0,
			"trips" => 0,
			"orders" => 0,
			"rating" => $carrier->rating,
			"nationality" => !is_null($carrier->nationality) ? $carrier->nationality : "",
			"id_number" => !is_null($carrier->id_number) ? $carrier->id_number : "",
			"birthday" => !is_null($carrier->birthday) ? $carrier->birthday : "",
			"default_address" => $carrier->default_address,
			"created_at" => $carrier->created_at,
			"last_login" => !is_null($carrier->user->last_login) ? $carrier->user->last_login : "",
		];

		if ($carrier->getMedia(Carrier::ID_IMAGE)->first()){
			$data[Carrier::ID_IMAGE] = $carrier->getMedia(Carrier::ID_IMAGE)->first()->getUrl('thumb');
		}

		if ($carrier->user->getMedia(User::PROFILE_IMAGE)->first()){
			$data[User::PROFILE_IMAGE] = $carrier->user->getMedia(User::PROFILE_IMAGE)->first()->getUrl('thumb');
		}

		if (isset($carrier->current_city) && !is_null($carrier->current_city)) {
			$data['current_city'] = ApiResponse::currentCityFromRole($carrier->user, 'carrier');
		}

		return $data;
	}
}
