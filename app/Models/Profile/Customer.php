<?php

/**
 * Created by Antony Repin
 * Date: 29.04.2017
 * Time: 1:52
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * App\Models\ProfileCustomer
 *
 * @property int $user_id
 * @property int $membership_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $name
 * @property \Carbon\Carbon $birth_date
 * @property bool $is_activated
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereBirthDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereIsActivated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereMembershipId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ProfileCustomer whereUserId($value)
 * @mixin \Eloquent
 */
class ProfileCustomer extends Model implements HasMediaConversions, AuditableContract
{
    use Auditable, Searchable, SoftDeletes, HasMediaTrait;

    const MEDIA_PICTURE = 'picture';

    protected $table = 'user_customer_profiles';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id', 'membership_id', 'name', 'birth_date', 'is_activated'];
    protected $casts = ['birth_date' => 'date', 'is_activated' => 'boolean'];

    public $incrementing = false;

    public function registerMediaConversions()
    {
        $this->addMediaConversion('fitted')
            ->fit(Manipulations::FIT_CROP, 400, 400);
    }
}
