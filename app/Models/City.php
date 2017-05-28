<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = ['name', 'status'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'name', 'status'];

	/**
	 * @var bool
	 */
	public $timestamps = false;

	public function country()
	{
		return $this->belongsTo('App\Models\Country');
	}


}
