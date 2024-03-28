<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 2:25
 */

namespace App\Repositories\Payment;

use App\Models\User;
use App\Models\User\Carrier;
use App\Repositories\CrudRepository;
use Jenssegers\Date\Date;

/**
 * Class PaymentRepository
 * @package App\Repositories\Payment
 */
class PaymentRepository extends CrudRepository implements PaymentRepositoryInterface
{
	/**
	 * @var
	 */
	protected $model = Payment::class;

	public function updateUserData($data, User $user)
	{
		$className = "\\App\\Models\\User\\" . ucfirst($user->roles()->first()->name);
		$user = $className::where('id', $user->id)->with('currentCity')->first();
		$data['card_expiry'] = Date::createFromFormat('m/y', $data['card_expiry'])
			->hour(0)
			->minute(0)
			->second(0);

		if (!$user->update($data)) {
			if (!is_null($user->validationErrors)) {
				foreach ($user->validationErrors['messages'] as $field => $messages) {
					foreach ($messages as $msg) {
						throw new \Exception($field . ": " . $msg, 409);
					}
				}
			}
		}

		return $user;
	}
}
