<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:41
 */

namespace App\Services\Settings;

use App\Models\Settings;
use App\Repositories\Setting\SettingRepositoryInterface;

/**
 * Class SettingsService
 * @package App\Services\Settings
 */
class SettingsService implements SettingsServiceInterface
{
    /**
     * @var SettingRepositoryInterface
     */
    protected $settingRepository;

    /**
     * SettingsService constructor.
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return null|string
     */
    public function get($key, $default = null) {
        /** @var Settings $record */
        $record = $this->settingRepository->find($key);

        if(!is_null($record)) {
            return $record->value;
        } else {
            return $default;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value) {
        /** @var Settings $record */
        $record = $this->settingRepository->find($key);

        if(!is_null($record)) {
            $this->settingRepository->edit($record, ['value' => $value]);
        } else {
            $this->settingRepository->create(['key' => $key, 'value' => $value]);
        }

        return true;
    }
}
