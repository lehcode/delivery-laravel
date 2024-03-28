<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:00
 */

namespace App\Http\Responses;

use League\Fractal\TransformerAbstract;

/**
 * Class MaintenanceModeResponse
 * @package App\Http\Responses
 */
class MaintenanceModeResponse extends TransformerAbstract {
    /**
     * @param $status
     * @return array
     */
    public function transform($status) {
        return [
            'maintenance_mode' => $status
        ];
    }
}
