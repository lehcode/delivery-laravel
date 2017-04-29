<?php
/**
 * Created by Antony Repin
 * Date: 29.04.2017
 * Time: 4:14
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * App\Models\ProfileDriver
 *
 * @property int $user_id
 * @property string $name
 * @property string $status
 * @property int $cash_limit
 * @property int $cash_current
 * @property string $notes
 * @property string $manager_id
 * @property string $referer
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \App\Models\UserCar $car
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereCashCurrent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereCashLimit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereManagerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereReferer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileDriver whereUserId($value)
 * @mixin \Eloquent
 */
class ProfileDriver extends Model implements HasMediaConversions, \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, Searchable, SoftDeletes, HasMediaTrait;

    const STATUS_ONLINE = 'online';
    const STATUS_OFFLINE = 'offline';

    const MEDIA_PICTURE = 'picture';

    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $table = 'user_driver_profiles';
    protected $fillable = [
        'user_id', 'name', 'status', 'referer'
    ];

    protected $visible = [
        'name', 'status', 'address',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|UserCar
     */
    public function car()
    {
        //return $this->hasOne(UserCar::class, 'user_id', 'user_id');
    }

    public function registerMediaConversions()
    {
        $this->addMediaConversion('fitted')
            ->fit(Manipulations::FIT_CROP, 400, 400);
    }
}
