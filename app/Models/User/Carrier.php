<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use App\Models\User;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrier extends Model implements HasMediaConversions, AuditableInterface
{
	use AuditableTrait, SoftDeletes, HasMediaTrait;

	const STATUS_ONLINE = 'online';
	const STATUS_OFFLINE = 'offine';

	const ID_IMAGE = 'id_scan';

	/**
	 * @var string
	 */
	protected $table = 'carriers';

	/**
	 * @var string
	 */
	protected $primaryKey = 'user_id';

	/**
	 * @var array
	 */
	protected $fillable = ['user_id', 'name', 'is_activated', 'is_online', 'current_city'];

	/**
	 * @var array
	 */
	protected $casts = ['is_activated' => 'boolean', 'is_online' => 'boolean'];

	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored', 'current_city'];

	/**
	 * @var array
	 */
	protected $appends = ['profile'];

	/**
	 * @var bool
	 */
	public $incrementing = false;
	
	protected $rules = [
		self::ID_IMAGE => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
		User::PROFILE_IMAGE => 'file|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
	];

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
	public function scopeOnline(Builder $builder) {
		return $builder->where('is_online', true);
	}
}
