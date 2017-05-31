<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 5:17
 */

namespace App\Extensions;


use App\Models\User;
use App\Models\User\Carrier;
use App\Models\User\Customer;
use Illuminate\Support\Facades\Auth;

trait ProfileAttributeTrait
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
	 * @throws \Exception
	 */
	public function getProfileAttribute()
	{

		$user = User::find($this->id);

		switch ($user->roles()->first()->name) {
			case User::ROLE_CUSTOMER:
				return Customer::find($this->id);
				break;

			case User::ROLE_CARRIER:
				return Carrier::find($this->id);
				break;

			case User::ROLE_ADMIN:
				return Admin::find($this->id);
				break;

			case User::ROLE_ROOT:
				return Root::find($this->id);
				break;

			default:
				throw new \Exception("Cannot get user profile.");

		}
	}
}
