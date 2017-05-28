<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 23:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country
{
	/**
	 * @var bool
	 */
	public $timestamps = false;

	public function cities()
	{
		return $this->hasMany('App\Models\City');
	}
}
