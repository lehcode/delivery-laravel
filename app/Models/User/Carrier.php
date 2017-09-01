<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use App\Extensions\ProfileAttributeTrait;
use App\Extensions\RfcDateTrait;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use Laratrust\Traits\LaratrustUserTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\Image\Manipulations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingInterface;
use Watson\Validating\ValidatingTrait;

/**
 * Class Carrier
 * @package App\Models\User
 */
class Carrier extends Model implements AuditableInterface, ValidatingInterface, HasMediaConversions
{
	use AuditableTrait,
		SoftDeletes,
		HasMediaTrait,
		ProfileAttributeTrait,
		LaratrustUserTrait,
		ValidatingTrait;

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
		'is_activated',
		'is_online',
		'current_city',
		'default_address',
		'birthday',
		'nationality',
		'id_number',
	];

	/**
	 * @var array
	 */
	protected $casts = [
		'is_activated' => 'boolean',
		'is_online' => 'boolean',
		'birthday' => 'date',
	];

	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];

	/**
	 * @var array
	 */
	protected $auditExclude = [
		'id',
		'nationality',
		'birthday',
		'default_address'
	];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $rules = [
		'default_address' => 'nullable|string',
		'current_city' => 'nullable|integer',
		'birthday' => 'date|nullable',
		'nationality' => 'string|nullable|min:2',
		'id_number' => 'string|required|min:3',
		'rating' => 'numeric|nullable',
		'notes' => 'string|nullable',
		'is_online' => 'boolean|required',
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
		return $this->belongsTo(User::class, 'id');
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

		$this->addMediaConversion('thumb')
			->fit(Manipulations::FIT_CROP, 120, 160);
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
