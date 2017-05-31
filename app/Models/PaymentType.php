<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = ['name'];

	/**
	 * @var array
	 */
	protected $visible = ['id', 'name'];

	/**
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function trips()
	{
		return $this->hasMany(Trip::class);
	}

}
