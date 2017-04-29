<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 3:09
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CrudRepository
 * @package App\Repositories
 */
interface CrudRepositoryInterface
{
    /**
     * @param int $id
     * @return $this->model
     */
    public function find($id);

    /**
     * @param array $params
     * @return Collection|null
     */
    public function findByParams(array $params);

    /**
     * @param array $params
     * @return $this->model
     */
    public function create(array $params);

    /**
     * @param Model $model
     * @param array $params
     * @param bool $unguard
     * @return bool
     */
    public function edit(Model $model, array $params, $unguard = false);

    /**
     * @param Model $model
     * @return bool
     */
    public function remove(Model $model);

    /**
     * @return Builder
     */
    public function getBuilder();
}
