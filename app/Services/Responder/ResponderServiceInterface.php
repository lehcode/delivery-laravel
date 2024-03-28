<?php

/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:36
 */

namespace App\Services\Responder;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface ResponderServiceInterface
{
    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data = [], $status = 200, array $headers = [], $options = 0);

    /**
     * @param Exception $e
     * @param int $errorNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(Exception $e, $errorNumber = null);

    /**
     * @param $data
     * @param int|null $length
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function objectResponse($data = null, $length = null, $key = 'items');

    /**
     * @param $items
     * @param string $transformerClass
     * @param int $page
     * @param array $transformerAttributes
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function fractal($items, $transformerClass = null, $page = 0, array $transformerAttributes = null);

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Eloquent $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent $query
     */
    public function filterQuery(Builder $query, Request $request);
}
