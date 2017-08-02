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
	 * @return Collection
	 */
	public function all();
}
