<?php
/**
 * Created by Antony Repin
 * Date: 26.06.2017
 * Time: 13:00
 */

namespace App\Http\Responses;

use App\Models\Shipment;

/**
 * Class ShipmentResponse
 * @package App\Http\Responses
 */
class ShipmentResponse extends ApiResponse
{
	/**
	 * @param Shipment $shipment
	 *
	 * @return array
	 */
	public function transform(Shipment $shipment)
	{

		$images = $shipment->getMedia(Shipment::MEDIA_COLLECTION)->map(function($item){
			return $item->getUrl();
		});


		$data = [
			'id' => $shipment->id,
			'category' => $shipment->category()->first(),
			'size' => $shipment->size()->first(),
			'image_url' => $images,
		];

		return $data;
	}
}
