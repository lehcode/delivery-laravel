<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 16:02
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * App\Models\Settings
 *
 * @property string $key
 * @property string $value
 * @property string $name
 * @property string $description
 * @property bool $is_public
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings public()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereIsPublic($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Settings whereValue($value)
 * @mixin \Eloquent
 */
class Settings extends Model implements AuditableContract
{
    use Auditable;

    const KEY_MAINTENANCE_MODE = 'maintenance_mode';

    protected $table = 'settings';
    protected $fillable = ['key', 'value', 'name', 'description', 'is_public'];
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $casts = ['is_public' => 'boolean'];

    /**
     * @param Builder $builder
     */
    public function scopePublic(Builder $builder) {
        $builder->where('is_public', true)->orderBy('name');
    }
}
