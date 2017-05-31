<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 2:15
 */

namespace App\Models\User;

use App\Extensions\ProfileAttributeTrait;
use App\Models\User;
use App\Models\Order;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 * @package App\Models\User
 */
class Customer extends Model implements HasMediaConversions, AuditableInterface
{
	use AuditableTrait,
		SoftDeletes,
		HasMediaTrait,
		ProfileAttributeTrait,
		LaratrustUserTrait;

	/**
	 * @var string
	 */
	protected $table = 'customers';

	/**
	 * @var array
	 */
	protected $fillable = ['id', 'name', 'is_activated'];
	/**
	 * @var array
	 */
	protected $casts = ['is_activated' => 'boolean'];
	/**
	 * @var array
	 */
	protected $auditableEvents = ['deleted', 'updated', 'restored'];

	/**
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $rules = [
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

}
