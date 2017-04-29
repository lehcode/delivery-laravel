<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:11
 */

namespace App\Repositories\UserSignupRequest;

use App\Models\UserSignupRequest;
use App\Repositories\CrudRepositoryInterface;

/**
 * Class UserSignupRepository
 * @package App\Repositories\UserSignupRequest
 */
interface UserSignupRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @param UserSignupRequest $signupRequest
     * @return bool
     */
    public function setProcessed(UserSignupRequest $signupRequest);
}
