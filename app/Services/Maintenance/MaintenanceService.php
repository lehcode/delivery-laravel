<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:38
 */

namespace App\Services\Maintenance;

use App\Models\Settings;
use App\Services\Settings\SettingsServiceInterface;

/**
 * Class MaintenanceService
 * @package App\Services\Maintenance
 */
class MaintenanceService implements MaintenanceServiceInterface
{
    /**
     * @var SettingsServiceInterface
     */
    protected $settingsService;

    /**
     * MaintenanceService constructor.
     * @param SettingsServiceInterface $settingsService
     */
    public function __construct(SettingsServiceInterface $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @return bool
     */
    public function isInMaintenanceMode() {
        return (bool)$this->settingsService->get(Settings::KEY_MAINTENANCE_MODE, false);
    }

    /**
     *
     */
    public function turnOn() {
        $this->settingsService->set(Settings::KEY_MAINTENANCE_MODE, true);
    }

    /**
     *
     */
    public function turnOff() {
        $this->settingsService->set(Settings::KEY_MAINTENANCE_MODE, false);
    }
}
