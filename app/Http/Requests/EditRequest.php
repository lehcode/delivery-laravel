<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:04
 */

namespace App\Http\Requests;

trait EditRequest {
    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        foreach($rules as $k=>$v) {
            if(preg_match('/required_if/', $v)) {
                $rules[$k] = $v;
            } elseif(preg_match('/image/', $v)) {
                $rules[$k] = '';
            } else {
                $rules[$k] = preg_replace('/required(\|?)/', '', $v);
            }
        }

        return array_filter($rules);
    }
}
