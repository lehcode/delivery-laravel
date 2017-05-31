<?php

namespace App\Models;

use App\Models\User\Customer;
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
		'payment_id',
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
		'payment_id' => 'integer',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function trip()
	{
		return $this->belongsTo(Trip::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function recipient()
	{
		return $this->belongsTo(Recipient::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function shipment()
	{
		return $this->belongsTo(Shipment::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

}
