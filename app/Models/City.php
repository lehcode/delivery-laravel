<?php

namespace App\Models;

use App\Models\User\Carrier;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 * @package App\Models
 */
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

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function country()
	{
		return $this->belongsTo(Country::class, 'country_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function carrier()
	{
		return $this->hasMany(Carrier::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class, 'current_city');
	}


}
