<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package App\Http\Controllers
 *
 * @SWG\Info(title="Barq Backend", version="1")
 * @SWG\Swagger(
 *     schemes={"http", "https"},
 *     host="52.29.57.93",
 *     basePath="/api",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 * )
 * @SWG\SecurityScheme(securityDefinition="api_key", type="apiKey", in="header", name="Authorization")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
