<?php

namespace App\Models\User;

use Jenssegers\Date\Date;
use Laratrust\LaratrustRole;
use App\Extensions\RfcDateTrait;

/**
 * Class Role
 * @package App\Models\User
 */
class Role extends LaratrustRole
{
	
	use RfcDateTrait;
	
	/**
	 * @var array
	 */
	protected $hidden = ['pivot', 'created_at', 'updated_at', 'id'];

	
}
