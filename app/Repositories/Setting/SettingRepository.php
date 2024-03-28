<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:56
 */

namespace App\Repositories\Setting;

use App\Models\Settings;
use App\Repositories\CrudRepository;

class SettingRepository extends CrudRepository implements SettingRepositoryInterface
{
    protected $model = Settings::class;
}
