<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use App\Models\User;
use App\Models\User\Role;
use App\Models\User\Permission;
use App\Repositories\User\UserRepositoryInterface;
use Propaganistas\LaravelIntl\Facades\Country;
use Watson\Validating\ValidationException;
use App\Models\City;

class SetupAclTablesSeed extends Seeder
{

	public $pwd = 'Qrab17';

	public $usersAmt = [
		'admin' => 1,
		'customer' => 9,
		'carrier' => 9,
	];

	public $phoneSfx = 0;

	public $cardTypes = ['Visa', 'MasterCard'];

	/**
	 * Run the database seeds.
	 *
	 * @return  void
	 */
	public function run()
	{

		$faker = Faker\Factory::create('en_GB');
		$cities = City::all();

		$this->command->info('Truncating User, Role and Permission tables');
		$this->truncateAclTables();

		$config = config('laratrust_seeder.role_structure');
		$userPermission = config('laratrust_seeder.permission_structure');
		$mapPermission = collect(config('laratrust_seeder.permissions_map'));

		foreach ($config as $key => $modules) {

			// Create a new role
			$role = Role::create([
				'name' => $key,
				'display_name' => ucwords(str_replace("_", " ", $key)),
				'description' => ucwords(str_replace("_", " ", $key))
			]);

			$this->command->info('Creating Role ' . strtoupper($key));

			// Reading role permission modules
			foreach ($modules as $module => $value) {
				$permissions = explode(',', $value);

				foreach ($permissions as $p => $perm) {
					$permissionValue = $mapPermission->get($perm);

					$permission = Permission::firstOrCreate([
						'name' => $permissionValue . '-' . $module,
						'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
						'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
					]);

					$this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);

					if (!$role->hasPermission($permission->name)) {
						$role->attachPermission($permission);
					} else {
						$this->command->info($key . ': ' . $p . ' ' . $permissionValue . ' already exist');
					}
				}
			}

			$this->command->info("Creating '{$key}' user(s)");

			if ($key === 'root') {

				$user = factory(User::class)->make([
					'email' => $key . '@barq.com',
					'phone' => '+375291111110',
				]);

				try {
					$user->save();
					$user->attachRole($role);
				} catch (ValidationException $e) {
					var_dump($user->toArray());
					print_r($e->getErrors());
					die();
				}

			} else {

				$tick = false;

				factory(User::class, $this->usersAmt[$key])->make()
					->each(function ($user) use ($role, &$tick, $key, $cities, $faker) {

						$this->phoneSfx++;

						if (!$tick) {
							$user->email = $role->name . '@barq.com';
							$user->phone = '+37529111111' . $this->phoneSfx;
							$tick = true;
						}

						try {
							$user->save();
							$user->attachRole($role);

							$created = null;

							switch ($key) {
								case 'customer':
									$created = factory(User\Customer::class)->create(
										[
											'id' => $user->id,
											'name' => $user->name,
											'card_name' => $user->name,
										]
									);
									break;

								case 'carrier':
									$created = User\Carrier::create(array_merge(
										[
											'id' => $user->id,
											'name' => $user->name
										],
										[
											'current_city' => $cities->random()->id,
											'default_address' => $faker->streetAddress,
											'notes' => $faker->text(128),
										]
									));
									break;
							}

							if (!is_null($created)) {
								if(!is_null($created->validationErrors) && !empty($created->validationErrors)){
									foreach ($created->validationErrors['messages'] as $messages) {
										foreach ($messages as $column => $errors) {
											foreach ($errors as $error) {
												throw new \Exception($column . ': ' . $error, 1);
											}
										}
									}
								}
							}

						} catch (\Exception $e) {
							var_dump($user->toArray());
							throw $e;
						}
					});
			}
		}

		// creating user with permissions
		if (count($userPermission)) {
			foreach ($userPermission as $key => $modules) {
				foreach ($modules as $module => $value) {
					$permissions = explode(',', $value);
					// Create default user for each permission set
					$user = app()->make(UserRepositoryInterface::class)->create([
						'name' => $faker->name,
						'email' => $key . '@barq.com',
						'phone' => $faker->phoneNumber,
						'password' => $this->pwd,
						'is_enabled' => true,
						'last_login' => Date::now(),
					]);
					foreach ($permissions as $p => $perm) {
						$permissionValue = $mapPermission->get($perm);

						$permission = \App\Models\User\Permission::firstOrCreate([
							'name' => $permissionValue . '-' . $module,
							'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
							'description' => ucfirst($permissionValue) . ' ' . ucfirst($module),
						]);

						$this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);

						if (!$user->hasPermission($permission->name)) {
							$user->attachPermission($permission);
						} else {
							$this->command->info($key . ': ' . $p . ' ' . $permissionValue . ' already exist');
						}
					}
				}
			}
		}
	}

	/**
	 * Truncates all the laratrust tables and the users table
	 * @return    void
	 */
	public function truncateAclTables()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		DB::table('permission_role')->truncate();
		DB::table('role_user')->truncate();
		User::truncate();
		Role::truncate();
		Permission::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
