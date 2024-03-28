<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 6:24
 */

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\CrudRepositoryInterface;

/**
 * Interface UserRepositoryInterface
 * @package App\Repositories\User
 */
interface UserRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @param User $user
     * @param $type
     * @return array
     */
    public function getUserDevicesToken(User $user, $type);

    /**
     * @param User $user
     * @return array
     */
    public function getUserDevicesTypes(User $user);

    /**
     * @param string $regId
     * @return bool
     */
    public function unregisterRegId($regId);
}
