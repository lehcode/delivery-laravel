<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use App\Extensions\ProfileAttributeTrait;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingInterface;
use Watson\Validating\ValidatingTrait;

/**
 * Class Customer
 * @package App\Models\User
 */
class Customer extends Model implements HasMediaConversions, AuditableInterface, ValidatingInterface
{
	use AuditableTrait,
		SoftDeletes,
		HasMediaTrait,
		ProfileAttributeTrait,
		LaratrustUserTrait,
		ValidatingTrait;

	/**
	 * @var string
	 */
	protected $table = 'customers';

	/**
	 * @var array
	 */
	protected $fillable = [
		'id',
		'is_activated',
		'current_city',
		'card_name',
		'card_type',
		'card_number',
		'card_expiry',
		'card_cvc',
	];

	/**
	 * @var array
	 */
	protected $casts = ['is_activated' => 'boolean', 'card_expiry' => 'date'];
	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];

	/**
	 * @var array
	 */
	protected $auditInclude = [
		'is_activated',
		'current_city',
		'card_name',
		'card_type',
		'card_number',
		'card_expiry',
		'card_cvc',
	];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Model validation rules
	 *
	 * @var array
	 */
	protected $rules = [
		'notes' => 'string',
		'current_city' => 'integer|exists:cities,id',
		'card_name' => 'string',
		'card_number' => 'ccn',
		'card_expiry' => 'date',
		'card_cvc' => 'cvc',
		'card_type' => 'in:Visa,MasterCard',
	];

	protected $hidden = ['updated_at'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function currentCity()
	{
		return $this->hasOne(City::class, 'id', 'current_city');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function orders()
	{
		return $this->hasMany(Order::class, 'customer_id');
	}

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);
	}

}
