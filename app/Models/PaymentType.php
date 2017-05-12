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


}
