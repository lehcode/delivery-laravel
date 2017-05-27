<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:48
 */

namespace App\Services\UserService;

use App\Mail\UserActivationMail;
use App\Models\ProfileCustomer;
use App\Models\ProfileDriver;
use App\Models\User;
use App\Models\UserCar;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Mail;
use Hash;

class UserService implements UserServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param array $params
     * @return bool
     */
    public function edit(User $user, array $params) {
        $funcName = 'edit' . ucfirst($user->roles()->first()->name);
        return $this->$funcName($user, $params);
    }

    /**
     * @param User $user
     * @param array $params
     * @return bool
     */
    protected function editCustomer(User $user, array $params) {
        $entityUser = array_only($params, $user->getFillable());
        $entityProfile = null;

        if(!is_null($user->profile)) {
            $entityProfile = array_only($params, $user->profile->getFillable());
        }

        Validator::make($params, [
            'email'         => ['email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'min:5',
            'phone'         => ['phone:AUTO', Rule::unique('users')->ignore($user->id)],
        ])->validate();

        return DB::transaction(function() use($entityUser, $entityProfile, $user, $params) {
            $this->userRepository->edit($user, $entityUser);

            if(!is_null($user->profile)) {
                $this->userRepository->edit($user->profile, $entityProfile);

                if(isset($params['remove_picture'])) {
                    $user->profile->clearMediaCollection(ProfileCustomer::MEDIA_PICTURE);
                }

                if(isset($params['picture']) && $params['picture'] instanceof UploadedFile) {
                    /** @var UploadedFile $picture */
                    $picture = $params['picture'];
                    $user->profile
                        ->clearMediaCollection(ProfileCustomer::MEDIA_PICTURE)
                        ->addMedia($picture)
                        ->toMediaLibrary(ProfileCustomer::MEDIA_PICTURE);
                }
            }

            return true;
        });
    }

    /**
     * @param User $user
     * @param array $params
     * @return bool
     */
    protected function editAdmin(User $user, array $params) {
        Validator::make($params, [
            'email'         => ['email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'min:5|confirmed',
            'phone'         => ['phone:AUTO', Rule::unique('users')->ignore($user->id)],
        ])->validate();

        return $this->userRepository->edit($user, array_only($params, $user->getFillable()));
    }

    /**
     * @param User $user
     * @param array $params
     * @return bool
     */
    protected function editDriver(User $user, array $params) {
        $entityUser = array_only($params, app()->make(User::class)->getFillable());
        $entityProfile = array_only($params, app()->make(ProfileDriver::class)->getFillable());
        $entityCar = isset($params['car']) && is_array($params['car']) ? $params['car'] : [];


        Validator::make($params, [
            'phone'         => ['phone:AUTO', Rule::unique('users')->ignore($user->id)],
            'name'          => 'min:1',
            'email'         => ['email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'min:5',
            'is_enabled'    => 'boolean',

            'cash_limit'    => 'integer:min:1',
            'membership_id' => 'exists:memberships,id',
            'notes'         => 'min:1',
        ])->validate();

        return DB::transaction(function() use($entityUser, $entityProfile, $entityCar, $params, $user) {
            /** @var User $user */
            $this->userRepository->edit($user, $entityUser);

            if(isset($params['membership_id'])) {
                $user->memberships()->sync([$params['membership_id']]);
            }

            $this->userRepository->edit($user->profile,array_merge([
                'user_id' => $user->id
            ], $entityProfile));

            if(isset($params['is_online']) && $params['is_online'] == true) {
                $this->userRepository->edit($user->profile, [
                    'status' => ProfileDriver::STATUS_ONLINE
                ]);
            } else {
                $this->userRepository->edit($user->profile, [
                    'status' => ProfileDriver::STATUS_OFFLINE
                ]);
            }

            foreach([ProfileDriver::MEDIA_PICTURE, ProfileDriver::MEDIA_LICENSE, ProfileDriver::MEDIA_ID_CARD] as $mediaName) {
                if (isset($params[$mediaName]) && $params[$mediaName] instanceof UploadedFile) {
                    /** @var UploadedFile $picture */
                    $picture = $params[$mediaName];
                    $user->profile
                        ->clearMediaCollection($mediaName)
                        ->addMedia($picture)
                        ->toMediaLibrary($mediaName);
                }
            }


            foreach([UserCar::MEDIA_CAR, UserCar::MEDIA_CAR_PLATE] as $mediaName) {
                if (isset($params['car'][$mediaName]) && $params['car'][$mediaName] instanceof UploadedFile) {
                    /** @var UploadedFile $picture */
                    $picture = $params['car'][$mediaName];
                    $user->car
                        ->clearMediaCollection($mediaName)
                        ->addMedia($picture)
                        ->toMediaLibrary($mediaName);
                }
            }

            return $user;
        });
    }

    /**
     * @param User $user
     */
    public function sendActivationLink(User $user) {
        $key = $this->makeActivationKey($user);
        Mail::to($user)->queue(new UserActivationMail($user, $key));
    }

    /**
     * @param User $user
     * @return string
     */
    protected function makeActivationKey(User $user) {
        return substr(sha1(json_encode([
            'id' => $user->id,
            'password' => $user->password,
            'created_at' => $user->created_at
        ])), 0, 10);
    }

    /**
     * @param User $user
     * @param string $key
     * @return bool
     */
    public function verifyKey(User $user, $key) {
        return ($key == $this->makeActivationKey($user));
    }

    /**
     * @param User $user
     * @param string $key
     * @return bool
     */
    public function activateUserByKey(User $user, $key) {
        if(!$this->verifyKey($user, $key)) {
            return false;
        }

        $this->userRepository->edit($user->profile, ['is_activated' => true]);
        return true;
    }

    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword(User $user, $password) {
        return Hash::check($password, $user->password);
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function changePassword(User $user, $password) {
        $user->password = $password;
        $user->save();
    }
}
