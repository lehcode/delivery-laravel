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
	const XDEBUG_PARAM = 'XDEBUG_SESSION_START';
	
	/**
	 * @param Request $request
	 * @param Closure $next
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle(Request $request, Closure $next) {

		$input = $request->except(self::XDEBUG_PARAM);
		$request->query->remove(self::XDEBUG_PARAM);
		$request->replace($input);

		if ($request->has(self::XDEBUG_PARAM)) {
			throw new \Exception("Request has ".self::XDEBUG_PARAM." variable.", 500);
		}

		return $next($request);
	}
}
