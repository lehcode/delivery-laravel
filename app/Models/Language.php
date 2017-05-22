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
use Laravel\Scout\Searchable as SearchableTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Watson\Validating\ValidatingTrait;

class Language extends Model implements AuditableInterface
{
    use AuditableTrait,
        Timestamp8601,
        SoftDeletes,
        //SearchableTrait,
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


