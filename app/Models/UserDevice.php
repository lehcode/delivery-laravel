<?php
/**
 * Created by Antony Repin
 * Date: 29.04.2017
 * Time: 0:32
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserDevice
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $device_id
 * @property string $reg_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereDeviceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereRegId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserDevice whereUserId($value)
 * @mixin \Eloquent
 */
class UserDevice extends Model
{
    //
}
