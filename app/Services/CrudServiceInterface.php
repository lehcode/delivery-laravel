<?php
/**
 * Created by Antony Repin
 * Date: 8/2/2017
 * Time: 23:39
 */

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

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
	 * @param Request $request
	 * @param         $id
	 *
	 * @return mixed
	 */
	public function byId(Request $request, $id);
}
