<?php
/**
 * Created by Antony Repin
 * Date: 06.07.2017
 * Time: 22:10
 */

namespace App\Extensions;

use Jenssegers\Date\Date;

/**
 * Class RfcDateTrait
 * @package App\Extensions
 */
trait RfcDateTrait
{
	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getUpdatedAtAttribute($value){
		return $this->rfcDate($value);
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function getCreatedAtAttribute($value){
		return $this->rfcDate($value);
	}

	public function rfcDate($value){
		return Date::createFromFormat('Y-m-d H:i:s', $value)->format('r');
	}
}
