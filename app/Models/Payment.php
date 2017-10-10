<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

	const STATUS_UNPAID = 'unpaid';
	const STATUS_PROCESSING = 'processing';
	const STATUS_PAID = 'paid';

	protected $fillable = ['order_id', 'amount', 'payment_type_id'];
	
	
}
