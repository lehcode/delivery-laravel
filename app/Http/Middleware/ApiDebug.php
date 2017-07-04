<?php
/**
 * Created by Antony Repin
 * Date: 04.07.2017
 * Time: 16:23
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class ApiDebug
 * @package App\Http\Middleware
 */
class ApiDebug
{
	/**
	 * @param Request $request
	 * @param Closure $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {

		$request->replace($request->except('XDEBUG_SESSION_START'));

		return $next($request);
	}
}
