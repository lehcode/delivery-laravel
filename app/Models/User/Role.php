<?php

namespace App\Models\User;

use Laratrust\LaratrustRole;

/**
 * Class Role
 * @package App\Models\User
 */
class Role extends LaratrustRole
{
	/**
	 * @var array
	 */
	protected $hidden = ['pivot', 'updated_at', 'id'];
}
