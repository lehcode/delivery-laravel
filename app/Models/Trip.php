<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

	/**
	 * @var array
	 */
	protected $fillable = ['time_length', 'payment_type', 'user_id'];
	
	/**
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	public function carrier(){
		return $this->belongsTo(User::class);
	}
}
