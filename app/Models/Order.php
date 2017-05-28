<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'departure_date',
		'expected_delivery_date',
		'recipient_name',
		'customer_id',
		'shipment_id',
		'trip_id',
	];

	/**
	 * @var array
	 */
	protected $dates = [
		'departure_date',
		'expected_delivery_date',
		'deleted_at',
		'created_at',
		'updated_at'];

	/**
	 * @var array
	 */
	protected $rules = [
		'departure_date' => 'required',
		'expected_delivery_date' => 'required',
		'recipient_name' => 'required|min:3',
		'customer_id' => 'required|integer',
		'shipment_id' => 'required|integer',
		'trip_id' => 'required|integer',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function trip()
	{
		return $this->hasOne(Trip::class, 'trip_id');
	}

	public function customer()
	{
		return $this->hasOne(User::class);
	}

	public function recipient()
	{
		return $this->hasOne(Recipient::class);
	}

	public function shipmentCategory()
	{
		return $this->hasOne(ShipmentCategory::class);
	}

}
