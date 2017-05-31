<?php

namespace App\Models;

use App\Models\User\Carrier;
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
		return $this->belongsTo(Country::class);
	}

	public function carrier()
	{
		return $this->hasMany(Carrier::class);
	}

	public function customer()
	{
		return $this->hasMany(Customer::class);
	}


}
