<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:37
 */

namespace App\Services\Maintenance;

/**
 * Interface MaintenanceServiceInterface
 * @package App\Services\Maintenance
 */
interface MaintenanceServiceInterface
{
    /**
     * @return bool
     */
    public function isInMaintenanceMode();

    /**
     * @return mixed
     */
    public function turnOn();

    /**
     * @return mixed
     */
    public function turnOff();
}
