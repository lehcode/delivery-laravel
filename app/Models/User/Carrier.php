<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrier extends Model implements HasMediaConversions, AuditableInterface
{
	use AuditableTrait, SoftDeletes, HasMediaTrait;

	const MEDIA_PICTURE = 'picture';

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
	protected $fillable = ['user_id', 'name', 'is_activated', 'is_online'];
	/**
	 * @var array
	 */
	protected $casts = ['is_activated' => 'boolean', 'is_online' => 'boolean'];
	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @throws \Spatie\Image\Exceptions\InvalidManipulation
	 */
	public function registerMediaConversions()
	{
		$this->addMediaConversion('fitted')
			->fit(Manipulations::FIT_CROP, 400, 400);
	}
	
	/**
	 * @return bool
	 */
	protected function getIsOnlineAttribute(){
		return true;
	}

	public function scopeOnline(Builder $builder) {
		return $builder->where('is_online', true);
	}
}
