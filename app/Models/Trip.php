<?php

namespace App\Models;

use App\Models\User\Carrier;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Trip
 * @package App\Models
 */
class Trip extends Model
{

	/**
	 * @var array
	 */
	protected $fillable = ['time_length', 'payment_type', 'carrier_id'];
	
	/**
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function carrier()
	{
		return $this->belongsTo(Carrier::class, 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function order()
	{
		return $this->belongsTo('App\Models\Order');
	}
}
