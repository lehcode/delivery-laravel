<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:36
 */

namespace App\Http\Middleware;

use App\Services\Maintenance\MaintenanceServiceInterface;
use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Class CheckMaintenance
 * @package App\Http\Middleware
 */
class CheckMaintenance {
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $maintenanceService = app()->make(MaintenanceServiceInterface::class);

        if($maintenanceService->isInMaintenanceMode() == true) {
            throw new ServiceUnavailableHttpException();
        }

        return $next($request);
    }
}
