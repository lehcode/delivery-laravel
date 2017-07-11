<?php
/**
 * Created by Antony Repin
 * Date: 07.07.2017
 * Time: 2:06
 */

namespace App\Http\Responses;


use App\Models\City;

/**
 * Class CityResponse
 * @package App\Http\Responses
 */
class CityResponse extends ApiResponse
{
	/**
	 * @param City $city
	 */
	public function transform(City $city)
	{

		$city->makeHidden('country_id');
		$data = $city->toArray();
		$data['country'] = $city->country;

		return $data;

	}
}
