<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:37
 */

namespace App\Services\Responder;

use App\Exceptions\MultipleExceptions;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResponderService implements ResponderServiceInterface
{
    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function response($data = [], $status = 200, array $headers = [], $options = 0)
    {
        /** @todo replace json_decode(json_encode()) bundle to something more adequate */

        if (env("JSON_NUMERIC_CHECK_BUG")) {
            $options = JSON_NUMERIC_CHECK;
        }

        if (config('app.debug') == true) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        try {
            $array = $this->array_filter_recursive(json_decode(json_encode($data), true), function ($value) {
                return $value !== null;
            });
        } catch (Exception $e) {
            foreach ($data as $k => $v) {
                if (is_object($v) && method_exists($v, 'toArray')) {
                    $v->toArray();
                }
            }

            throw $e;
        }

        /* if(is_null(Request::header('Origin'))) {
            $array = $this->array_filter_recursive($array, function($value, $key) {
                return $key !== 'translations';
            });
        } */

        return response()->json($array, $status, $headers, $options);
    }

    /**
     * @param Exception $e
     * @param int $errorNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(Exception $e, $errorNumber = null)
    {
        $errorNumber = is_null($errorNumber) ? $e->getCode() : $errorNumber;

        return $this->response([
            'status' => 'error',
            'code' => !is_null($e->getCode()) ? $e->getCode() : $errorNumber,
            'message' => $e instanceof MultipleExceptions ? $e->getMessages() : [$e->getMessage()]
        ], $errorNumber >= 400 && $errorNumber < 600 ? $errorNumber : 500);
    }

    /**
     * @param null $data
     * @param null $length
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function objectResponse($data = null, $length = null, $key = 'items')
    {
        if ($data instanceof Fractal) {
            /** @var Fractal $data */
            $data = $data->toArray();

            if (isset($data['data'])) {
                $data = $data['data'];
            }
        } elseif (method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        if (is_array($data)) {
            ksort($data);
        }

        return $this->response([
            'status' => 'success',
            'length' => !is_null($length) ? $length : count($data),
            $key => $data,
        ], 200);
    }

    /**
     * @param $array
     * @param string|Callable $callback
     * @param bool $remove_empty_arrays
     * @return mixed
     */
    protected function array_filter_recursive($array, $callback = '', $remove_empty_arrays = false)
    {
        foreach ($array as $key => & $value) { // mind the reference
            if (!is_null($callback) && !$callback($value, $key))
                unset($array[$key]);

            if (is_array($value) && isset($array[$key])) {
                $value = $this->array_filter_recursive($value, $callback);

                if ($remove_empty_arrays && empty($value))
                    unset($array[$key]);
            }
        }

        unset($value); // kill the reference
        return $array;
    }

    /**
     * @param            $items
     * @param null       $transformerClass
     * @param int        $page
     * @param array|NULL $transformerAttributes
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function fractal($items, $transformerClass = NULL, $page = 0, array $transformerAttributes = NULL)
    {
        if (is_null($transformerAttributes)){
            $transformerAttributes = [];
        }

        if (!is_null($transformerClass)) {
            $reflector = new \ReflectionClass($transformerClass);
            $transformer = $reflector->newInstanceArgs($transformerAttributes);
        } else {
            $transformer = null;
        }

        $fractal = fractal();

        if ($items instanceof Collection) {
            if ($page > -1) {
                $paginator = new LengthAwarePaginator($items, $items->count(), config('laravel-fractal.per_page'), $page);
                $response = $fractal->collection($paginator, $transformer);
            } else {
                $response = $fractal->collection($items, $transformer);
            }
        } elseif ($items instanceof \Illuminate\Database\Eloquent\Builder) {
            /** @var \Illuminate\Database\Eloquent\Builder $items */
            if ($page > -1) {
                $paginator = $items->paginate(config('laravel-fractal.per_page'), ['*'], '', $page);
                $items = $paginator->getCollection();
                $response = $fractal->collection($items, $transformer);
            } else {
                if (isset($items->count)) {
                    $totalCount = $items->count;
                } else {
                    $totalCount = $items->count();
                }

                $response = $fractal->collection($items->get(), $transformer);
            }
        } elseif ($items instanceof Model) {
            $collection = (new Collection())->add($items);
            $response = $fractal->collection($collection, $transformer);
            $paginator = new LengthAwarePaginator($collection, $collection->count(), config('laravel-fractal.per_page'), $page);
        } elseif (is_scalar($items)) {
            /** @var \Spatie\Fractal\Fractal $response */
            $response = $fractal->item($items, $transformer);
        } elseif (is_array($items)) {
            /** @var \Spatie\Fractal\Fractal $response */
            $response = $fractal->item($items, $transformer);
        } elseif (is_null($items)) {
            return $this->response(['status' => 'success']);
        } else {
            throw new \Exception("Incorrect data type: " . get_class($items));
        }

        /** @var \Spatie\Fractal\Fractal $response */
        if (isset($paginator)) {
            $paginatorAdapter = new IlluminatePaginatorAdapter($paginator);
            $response->paginateWith($paginatorAdapter);
        }

        $serialized = $response->jsonSerialize();

        return $this->response(array_merge(['status' => 'success'], [
            'data' => array_except($serialized, ['meta']),
            'meta' => isset($serialized['meta']) ? $serialized['meta'] : (isset($totalCount) ? [
                'pagination' => [
                    'total' => $totalCount
                ]
            ] : null)
        ]));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Eloquent $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Eloquent $query
     */
    public function filterQuery(Builder $query, Request $request)
    {
        $filterPhrase = $request->get('filter');
        $conditions = $request->get('conditions', []);
        $builder = $query;
        $sortFields = explode("|", $request->get('sort'));

        list($sortField, $sortDirection) = count($sortFields) == 2 ? $sortFields : [$sortFields[0], "ASC"];

        if (!empty($sortField)) {
            $attributes = array_merge($query->getModel()->getFillable(), $query->getModel()->getDates());

            if (!in_array($sortField, array_merge($attributes, ['id']))) {
                throw new BadRequestHttpException();
            }

            if (method_exists($query->getModel(), 'getTranslatableAttributes')) {
                $translatable = $query->getModel()->getTranslatableAttributes();

                if (in_array($sortField, $translatable)) {
                    $locale = app()->getLocale();
                    $builder->orderByRaw("JSON_EXTRACT(name, '$.{$locale}') $sortDirection");
                }
            } else {
                /** @var Builder $builder */
                $builder = $builder->orderBy($sortField, $sortDirection);
            }
        }

        if (!empty($conditions) && !is_array($conditions) && preg_match('/^[\{\[]/', $conditions)) {
            $conditions = json_decode($conditions, true);

            if (isset($conditions['field'])) {
                $conditions = [$conditions];
            } elseif (!isset($conditions[0]['field'])) {
                $conditions = null;
            }
        }

        if (is_array($conditions) && !empty($conditions)) {
            foreach ($conditions as $condition) {
                if (isset($condition['field']) && isset($condition['value'])) {
                    $field = $condition['field'];
                    $operator = isset($condition['operator']) ? $condition['operator'] : '=';
                    $value = $condition['value'];

                    if (is_array($value)) {
                        $builder = $builder->whereIn($field, $value);
                    } else {
                        $builder = $builder->where($field, $operator, $value);
                    }
                } else {
                    throw new BadRequestHttpException("Invalid condition detected");
                }
            }
        }

        if (!isset($builder->count)) {
            $builder->count = $builder->count();
        }

        if ($request->has('start')) {
            $builder->limit((int)$request->get('count'));
            $builder->skip((int)$request->get('start'));
        }

        if (!empty($filterPhrase) && method_exists($builder->getModel(), 'bootSearchable')) {
            $match = $builder->getModel()->search($filterPhrase)->get('id')->pluck('id')->toArray();
            $builder = $builder->whereIn('id', $match);
        }

        return $builder;
    }
}
