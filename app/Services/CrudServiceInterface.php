<?php
/**
 * Created by Antony Repin
 * Date: 8/2/2017
 * Time: 23:39
 */

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface CrudServiceInterface
 * @package App\Services
 */
interface CrudServiceInterface
{
	/**
	 * Get Collection of items from repository
	 *
	 * @return Collection
	 */
	public function all();

	/**
	 * Get single item from repository
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function byId($id);
}
