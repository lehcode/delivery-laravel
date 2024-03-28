<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 14:59
 */

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;
use Fractal;

/**
 * Class ApiResponse
 * @package App\Http\Responses
 */
class ApiResponse extends TransformerAbstract
{
    /**
     * @param $item
     * @param $transformerClass
     *
     * @return mixed
     */
    public function includeTransformedItem($item, $transformerClass)
    {
        $collection = new Collection([$item]);
        return current(Fractal::collection($collection, $transformerClass)->jsonSerialize());
    }

    /**
     * @param $collection
     * @param $transformerClass
     *
     * @return array
     */
    public function includeTransformedCollection($collection, $transformerClass)
    {
        $output = array_except(Fractal::collection($collection, $transformerClass)->jsonSerialize(), ['meta']);
        return $output;
    }

    /**
     * @param User $user
     * @param string $role
     *
     * @return array
     */
    static public function currentCityFromRole(User $user, $role = null){
        $location = $user->$role->currentCity()->with('country')->first();
        $city = $location->toArray();
        $city['country'] = $location->country->toArray();

        return $city;
    }
}
