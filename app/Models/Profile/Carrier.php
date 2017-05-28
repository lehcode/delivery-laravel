<?php
/**
 * Created by Antony Repin
 * Date: 29.04.2017
 * Time: 4:14
 */

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use App\Models\Trip;

class Carrier extends Model implements HasMediaConversions, AuditableInterface
{
	use AuditableTrait,
		Searchable,
		SoftDeletes,
		HasMediaTrait;

	const STATUS_ONLINE = 'online';
	const STATUS_OFFLINE = 'offline';

	/**
	 * @var bool
	 */
	public $incrementing = false;
	
	/**
	 * @var array
	 */
	protected $fillable = ['user_id', 'name', 'status'];

	/**
	 * @var array
	 */
	protected $visible = [
		'name', 'status', 'address',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function trip()
	{
		return $this->hasMany(Trip::class);
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
