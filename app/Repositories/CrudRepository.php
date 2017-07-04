<?php

/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 3:04
 */

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class CrudRepository
 * @package App\Repositories
 */
class CrudRepository implements CrudRepositoryInterface
{
	/**
	 * @var mixed
	 */
	protected $model = null;

	/**
	 * @var null
	 */
	protected $user = null;

	/**
	 * @param int $id
	 * @return $this->model
	 */
	public function find($id)
	{
		$m = $this->model;
		$result = $m::findOrFail($id);
		
		return $result;
	}

	/**
	 * @param array $params
	 * @return Collection|null
	 */
	public function findByParams(array $params)
	{
		$m = $this->model;
		return $m::where($params)->get();
	}

	/**
	 * @param array $params
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function create(array $params)
	{
		$m = $this->model;
		$created = $m::create($params);

		if (!$created->isValid()) {
			$errors = $created->getErrors()->messages();
			foreach ($errors as $req => $error) {
				foreach ($error as $text){
					throw new \Exception($text, 1);
				}
			}
		}
		
		return $created;
	}

	/**
	 * @param array $params
	 * @return $this->model
	 */
	public function edit(Model $model, array $params, $unguard = false)
	{
		$save = function ($model, $params) {
			return $model->fill($params)->save();
		};

		if ($unguard == true) {
			return $model::unguarded(function () use ($model, $params, $save) {
				return $save($model, $params);
			});
		} else {
			return $save($model, $params);
		}
	}

	/**
	 * @param Model $model
	 * @return bool
	 */
	public function remove(Model $model)
	{
		return $model->delete();
	}

	/**
	 * @return Builder
	 */
	public function getBuilder()
	{
		$m = $this->model;
		return $m::select();
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function store(array $data)
	{
		$m = $this->model;
		return $m::create($data);
	}


}
