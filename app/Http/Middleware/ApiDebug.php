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
	 * @throws \Exception
	 */
	public function handle(Request $request, Closure $next) {

		$input = $request->except('XDEBUG_SESSION_START');
		$request->query->remove('XDEBUG_SESSION_START');
		$request->replace($input);

		if ($request->has('XDEBUG_SESSION_START')) {
			throw new \Exception("Request has XDEBUG_SESSION_START variable.", 500);
		}

		return $next($request);
	}
}
