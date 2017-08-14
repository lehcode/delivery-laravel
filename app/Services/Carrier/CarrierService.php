<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:35
 */

namespace App\Services\Carrier;

use App\Exceptions\ModelValidationException;
use App\Exceptions\RequestValidationException;
use App\Http\Requests\EditUserProfileRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\User\Carrier;
use App\Repositories\User\UserRepository;
use App\Services\CrudServiceInterface;
use App\Services\UserService\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Jenssegers\Date\Date;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class CarrierService
 * @package App\Services\Carrier
 */
class CarrierService implements CrudServiceInterface
{
	/**
	 * @var UserService
	 */
	protected $userService;

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * CarrierService constructor.
	 *
	 * @param UserService    $userService
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserService $userService,
	                            UserRepository $userRepository)
	{
		$this->userService = $userService;
		$this->userRepository = $userRepository;
	}

	/**
	 * @return mixed
	 */
	public function getTrips()
	{
		return Trip::all()
			->sortByDesc('created_at');
	}

	/**
	 * @return Collection
	 */
	public function all()
	{
		return Carrier::all()
			->sortByDesc('created_at');
	}

	/**
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function byId($id)
	{

		\Validator::make(['id' => $id], [
			'id' => 'required|regex:/' . User::UUID_REGEX . '/',
		])->validate();

		return Carrier::findOrFail($id);
	}

	/**
	 * @param EditUserProfileRequest $request
	 * @param                        $id
	 *
	 * @return mixed
	 * @throws RequestValidationException
	 */
	public function update(EditUserProfileRequest $request, $id)
	{

		$data = $request->except(['_method']);
		$user = User::findOrFail($id);
		$user->load('carrier');

		if ($request->validate() !== true){
			throw new RequestValidationException($request->messages());
		}

		return \DB::transaction(function () use ($request, $user, $data) {

			if ($request->has('remove_id_scan')) {
				$user->clearMediaCollection(Carrier::ID_IMAGE);
			}

			if ($request->has('name')) {
				$user->name = $data['name'];
			}

			if ($request->has('email')) {
				$user->email = $data['email'];
			}
			
			if ($request->has('phone')) {
				if ($user->phone !== $data['phone']){
					$user->phone = $data['phone'];
				}
			}

			if ($request->has('is_enabled')) {
				$user->is_enabled = (bool) $data['is_enabled'];
			}

			if ($request->has('default_address')) {
				$user->carrier->default_address = $data['default_address'];
			}

			if ($request->has('location')) {
				$user->carrier->current_city = $data['location.city'];
			}

			if ($request->has('notes')) {
				$user->carrier->notes = $data['notes'];
			}

			if ($request->has('birthday')) {
				$user->carrier->birthday = Date::createFromFormat("Y-m-d", $data['birthday']);
			}

			if ($request->has(Carrier::ID_IMAGE)) {
				if ($data[Carrier::ID_IMAGE] instanceof UploadedFile) {
					$img = $data[Carrier::ID_IMAGE];
					$user->carrier->clearMediaCollection(Carrier::ID_IMAGE)
						->addMedia($img)
						->usingFileName($img->hashName())
						->toMediaCollection(Carrier::ID_IMAGE, 's3');
				}
			}

			if ($request->has(User::PROFILE_IMAGE)) {
				if ($data[User::PROFILE_IMAGE] instanceof UploadedFile) {
					$img = $data[User::PROFILE_IMAGE];
					$user->clearMediaCollection(User::PROFILE_IMAGE)
						->addMedia($img)
						->usingFileName($img->hashName())
						->toMediaCollection(User::PROFILE_IMAGE, 's3');
				}
			}

			$user->carrier->saveOrFail();
			$user->fill($data)->saveOrFail();

			return $user->carrier;
		});

	}


}
