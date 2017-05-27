<?php
/**
 * Created by Antony Repin
 * Date: 25.05.2017
 * Time: 3:37
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Class UserSignup
 * @package App\Models
 */
class UserSignup extends Model implements Auditable
{
	use SoftDeletes, AuditableTrait, Searchable;

	/**
	 * @var string
	 */
	protected $table = 'user_signup_requests';
	/**
	 * @var array
	 */
	protected $fillable = ['email', 'name', 'phone', 'is_processed'];
	/**
	 * @var array
	 */
	protected $casts = ['is_processed' => 'boolean'];

	/**
	 * @param Builder $builder
	 *
	 * @return mixed
	 */
	public function scopeNotProcessed(Builder $builder)
	{
		return $builder->where('is_processed', false)->orderBy('id', 'DESC');
	}


}
