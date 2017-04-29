<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 20:21
 */

namespace App\Extensions;

/**
 * Class Timestamp8601
 * @package App\Extensions
 */
trait Timestamp8601
{
    /**
     * @return null
     */
    public function getCreatedAtAttribute()
    {
        return isset($this->attributes['created_at']) ? date('c', strtotime($this->attributes['created_at'])) : null;
    }

    /**
     * @return null
     */
    public function getUpdatedAtAttribute()
    {
        return isset($this->attributes['updated_at']) ? date('c', strtotime($this->attributes['updated_at'])) : null;
    }
}
