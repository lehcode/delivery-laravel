<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:41
 */

namespace App\Services\Settings;

/**
 * Class SettingsService
 * @package App\Services\Settings
 */
interface SettingsServiceInterface
{
    /**
     * @param string $key
     * @param null|mixed $default
     * @return null|string
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value);
}
