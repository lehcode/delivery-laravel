<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:35
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\FileBag;
use \Mimey\MimeTypes;

/**
 * Class Morph
 * @package App\Http\Middleware
 */
class Morph {
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if($locale = $request->header('Locale'))
            app()->setLocale($locale);

        $files = [];

        $morphItem = function($items, $keys = []) use(&$files, &$morphItem) {
            $ret = [];

            foreach($items as $key=>$value) {
                if(is_array($value)) {
                    $ret[$key] = $morphItem($value, array_merge($keys, [$key]));

                    if(empty($ret[$key])) {
                        unset($ret[$key]);
                    }
                } elseif (is_string($value) && substr($value, 0, 5) == 'data:') {
                    if(empty($keys)) {
                        $files[$key] = $this->b64ToFile($value);
                    } else {
                        $files[implode('_', $keys)][$key] = $this->b64ToFile($value);
                    }
                } elseif (is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.(\d{3})?)Z$/', $value, $parts)) {
                    $ret[$key] = date("Y-m-d H:i:s", gmmktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]));
                } elseif(!is_null($value)) {
                    $ret[$key] = $value;
                }
            }

            return $ret;
        };

        $request->replace($morphItem($request->all()));
        $request->files = new FileBag($files);

        return $next($request);
    }

    /**
     * @param $b64url
     * @return \Illuminate\Http\UploadedFile
     * @throws \Exception
     */
    protected function b64ToFile($b64url) {
        list($urlSpec, $base64data) = explode(',', str_replace('data:', '', $b64url), 2);
        list($mimeType, $encodeType) = explode(';', $urlSpec, 2);
        $tmp = tempnam(sys_get_temp_dir(), 'dataurl_');

        switch($encodeType) {
            case 'base64':
                $h = fopen($tmp, 'w+');
                fwrite($h, base64_decode($base64data));
                fclose($h);

                break;
            default:
                throw new \Exception("Invalid data-url encoding type!");
        }

        $mimey = new MimeTypes();

        if(!$extension = $mimey->getExtension($mimeType)) {
            $extension = 'raw';
        }

        return new \Illuminate\Http\UploadedFile($tmp, tempnam(sys_get_temp_dir(), '').'.'.$extension, $mimeType, filesize($tmp), $error = null, $test = true);
    }
}
