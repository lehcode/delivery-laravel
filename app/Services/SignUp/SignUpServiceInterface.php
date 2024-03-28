<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:47
 */

namespace App\Services\SignUp;

use App\Http\Requests\Admin\SignupAdminRequest;
use App\Http\Requests\SignupCustomerRequest;
use App\Models\User;
use App\Models\User\Customer;
use App\Models\UserSignupRequest;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SignUpService
 * @package App\Services\SignUp
 */
interface SignUpServiceInterface
{
	/**
	 * @param SignupCustomerRequest $request
	 *
	 * @return Customer
	 */
	public function customer(SignupCustomerRequest $request);

	/**
	 * @param SignupAdminRequest $request
	 *
	 * @return mixed
	 */
	public function admin(SignupAdminRequest $request);

	/**
	 * @param array $params
	 * @return Model|UserSignupRequest|null
	 */
	public function request(array $params);
}
