<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use App\Extensions\ProfileAttributeTrait;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Carrier
 * @package App\Models\User
 */
class Carrier extends Model implements HasMediaConversions, AuditableInterface
{
	use AuditableTrait,
		SoftDeletes,
		HasMediaTrait,
		ProfileAttributeTrait,
		LaratrustUserTrait;

	const STATUS_ONLINE = 'online';
	const STATUS_OFFLINE = 'offline';

	/**
	 *
	 */
	const ID_IMAGE = 'id_scan';

	/**
	 * @var string
	 */
	protected $table = 'carriers';

	/**
	 * @var array
	 */
	protected $fillable = [
		'id',
		'is_activated',
		'is_online',
		'current_city',
		'default_address',
		//self::ID_IMAGE,
	];

	/**
	 * @var array
	 */
	protected $casts = ['is_activated' => 'boolean', 'is_online' => 'boolean'];

	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored', 'current_city'];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $rules = [
		'default_address' => 'required|string',
		'current_city' => 'nullable|integer',
		//self::ID_IMAGE => 'required|string|unique:carriers,id_scan',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function trips()
	{
		return $this->hasMany(Trip::class, 'id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function currentCity()
	{
		return $this->belongsTo(City::class, 'current_city');
	}

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);
	}

	/**
	 * @param Builder $builder
	 *
	 * @return mixed
	 */
	public function scopeOnline(Builder $builder)
	{
		return $builder->where('is_online', true);
	}
}
