<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 14:56
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class Locale
 */
class Locale {
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($locale = $request->header('Locale'))
            app()->setLocale($locale);

        /* This part of code wrote to implement a new method to set a translations */
        $request->replace($this->mutateTranslations($request->all()));

        return $next($request);
    }

    /**
     * @param $request
     * @return mixed
     */
    protected function mutateTranslations($request) {
        foreach((array)$request as $key=>$value) {
            if($key == 'attributes') continue;

            if(is_array($value) && $key != 'translations') {
                $request[$key] = $this->mutateTranslations($value);
            } elseif($key == 'translations' && isset($request['translations'])) {
                foreach($request['translations'] as $locale => $fields) {
                    foreach($fields as $key => $value) {
                        if(isset($request[$key]) && !is_array($request[$key])) {
                            $request[$key] = [];
                        }

                        $request[$key][$locale] = $value;
                    }
                }

                unset($request['translations']);
            } else {
                if(!(is_array($request[$key]) && $key != 'translations')) {
                    $request[$key] = $value;
                }
            }
        }

        return $request;
    }
}
