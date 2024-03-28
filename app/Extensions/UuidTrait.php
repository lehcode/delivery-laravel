<?php

/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 2:50
 */

namespace App\Extensions;

use Webpatser\Uuid\Uuid;


trait UuidTrait
{
    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::generate()->string;
        });
    }
}
