<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:47
 */

namespace App\Services\SignUp;

use App\Models\User;
use App\Models\UserSignupRequest;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SignUpService
 * @package App\Services\SignUp
 */
interface SignUpServiceInterface
{
    /**
     * @param array $params
     * @return User|null
     */
    public function customer(array $params);

    /**
     * @param array $params
     * @return Model|User|null
     */
    public function admin(array $params);

    /**
     * @param array $params
     * @return Model|UserSignupRequest|null
     */
    public function request(array $params);
}
