<?php
/**
 * Created by Antony Repin
 * Date: 03.06.2017
 * Time: 15:36
 */

namespace App\Http\Middleware;

use Closure;

/**
 * Class SetApiHost
 * @package App\Http\Middleware
 */
class SetApiHost
{
	/**
	 * @param         $request
	 * @param Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		switch (config('app.env')){
			case 'production':
				define("API_HOST", "barqapp.co");
				break;
			case 'testing':
				define("API_HOST", "test.barqapp.co");
				break;
			case 'staging':
				define("API_HOST", "stage.barqapp.co");
				break;
			default:
				define("API_HOST", config('app.url'));
		}

		return $next($request);
	}
}
