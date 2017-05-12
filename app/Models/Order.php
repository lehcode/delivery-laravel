<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $fillable = [
		'departure_date',
		'expected_delivery_date',
		'recipient_name',
		'customer_id',
		'driver_id',
		'shipment_id',
		'route_id',
		'trip_id',
	];

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	protected $rules = [
		'name' => 'required',
		'email' => 'required|email|unique:users,email',
		'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
		'password' => 'required|min:5'
	];

}
