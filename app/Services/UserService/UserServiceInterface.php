<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:49
 */

namespace App\Services\UserService;

use App\Models\User;

/**
 * Interface UserServiceInterface
 * @package App\Services\UserService
 */
interface UserServiceInterface
{
    /**
     * @param User $user
     * @param array $params
     * @return bool
     */
    public function edit(User $user, array $params);

    /**
     * @param User $user
     */
    public function sendActivationLink(User $user);

    /**
     * @param User $user
     * @param string $key
     * @return bool
     */
    public function verifyKey(User $user, $key);

    /**
     * @param User $user
     * @param string $key
     * @return bool
     */
    public function activateUserByKey(User $user, $key);

    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword(User $user, $password);

    /**
     * @param User $user
     * @param string $password
     */
    public function changePassword(User $user, $password);
}
