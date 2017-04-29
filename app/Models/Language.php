<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 16:13
 */

namespace App\Models;

use App\Extensions\Timestamp8601;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use Watson\Validating\ValidatingTrait;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string $locale
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $flag
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable,
        Timestamp8601,
        SoftDeletes,
        //Searchable,
        ValidatingTrait;

    /**
     * @var array
     */
    protected $fillable = ['name', 'locale', 'flag'];
    /**
     * @var array
     */
    protected $visible = ['id', 'name', 'locale', 'flag', 'created_at', 'updated_at'];
    /**
     * @var bool
     */
    protected $throwValidationExceptions = true;
    /**
     * @var bool
     */
    protected $injectUniqueIdentifier = true;

    /**
     * @var array
     */
    protected $rules = [
        'name' => 'required|unique:languages,name',
        'locale' => 'required|unique:languages,locale',
        'flag' => 'required',
    ];
}


