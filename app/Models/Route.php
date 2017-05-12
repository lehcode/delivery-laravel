<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Route
 * @package App\Models
 */
class Route extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = ['from_city', 'to_city', 'type', 'departure_date', 'coords'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'from_city_id', 'to_city_id'];

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

}
