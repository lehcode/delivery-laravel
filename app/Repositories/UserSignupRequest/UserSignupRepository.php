<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:09
 */

namespace App\Repositories\UserSignupRequest;

use App\Models\UserSignupRequest;
use App\Repositories\CrudRepository;

/**
 * Class UserSignupRepository
 * @package App\Repositories\UserSignupRequest
 */
class UserSignupRepository extends CrudRepository implements UserSignupRepositoryInterface
{
    /**
     * @var UserSignupRequest $model
     */
    protected $model = UserSignupRequest::class;

    /**
     * @param UserSignupRequest $signupRequest
     * @return bool
     */
    public function setProcessed(UserSignupRequest $signupRequest) {
        $signupRequest->is_processed = true;
        return $signupRequest->save();
    }
}
