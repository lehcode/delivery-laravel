<?php
/**
 * Created by Antony Repin
 * Date: 26.06.2017
 * Time: 15:44
 */

namespace App\Http\Responses;

use App\Models\Recipient;


/**
 * Class RecipientResponse
 * @package App\Http\Responses
 */
class RecipientResponse extends ApiResponse
{
	/**
	 * @param Recipient $recipient
	 *
	 * @return array
	 */
	public function transform(Recipient $recipient)
	{
		$data = [
			'id' => $recipient->id,
		];
		
		$data = array_merge($data, $recipient->toArray());

		return $data;
	}
}
