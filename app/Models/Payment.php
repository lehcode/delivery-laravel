<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

	const STATUSES = ['not_paid', 'processing', 'paid'];

	protected $fillable = ['order_id', 'amount', 'payment_type_id'];
	
	
}
