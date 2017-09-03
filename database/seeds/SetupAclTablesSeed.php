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
use Webpatser\Uuid\Uuid;

class SetupAclTablesSeed extends Seeder
{

	public $pwd = 'Qrab17';

	public $usersAmt = [
		'admin' => 1,
		'customer' => 10,
		'carrier' => 15,
	];

	public $cardTypes = ['Visa', 'MasterCard'];

	/**
	 * Run the database seeds.
	 *
	 * @throws Exception
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
					'username' => $key . '@barq.com',
					'is_enabled' => true,
					'password' => $this->pwd,
					'last_login' => Date::now()->subHours(rand(1, 48))
				]);

				if (!$user->isValid()) {
					$errors = $user->getErrors();
					foreach ($errors as $req => $error) {
						throw new \Exception($error, 1);
					}
				}

				try {
					$user->save();
					$user->attachRole($role);
				} catch (ValidationException $e) {
					var_dump($user->toArray());
					print_r($e->getErrors());
					die($e->getMessage() . "\n");
				}

			} else {

				$pre = true;

				factory(User::class, $this->usersAmt[$key])->make([
					'id' => Uuid::generate(4)->string,
					'email' => $faker->freeEmail,
					'username' => $faker->userName,
					'password' => $this->pwd,
					'phone' => '+37529' . mt_rand(1111111, 9999999),
					'last_login' => Date::now()->subHours(rand(1, 48)),
					'is_enabled' => true,
				])
					->each(function ($user) use ($role, &$pre, $key, $cities, $faker) {

						$user->name = rand(1, 5) === 5 ? $faker->firstName . ' ' . $faker->lastName : null;
						$user->last_login = Date::now()->subHours(rand(1, 48));
						$user->password = $this->pwd;

						if ($pre === true) {

							if (in_array($key, User::ADMIN_ROLES)){
								$user->username = $role->name. '@barq.com';
							} else {
								$user->username = $role->name;
							}

							$user->email = $role->name . '@barq.com';
							$user->phone = '+37529' . '11111' . rand(11, 99);
							$user->is_enabled = true;
							$user->last_login = Date::now()->subHours(rand(1, 48));
							$pre = false;
						} else {
							$user->username = $faker->userName;
							$user->email = $faker->freeEmail;
							$user->is_enabled = rand(1, 8) === 8 ? false : true;
							$user->phone = rand(1, 9) === 9 ? null : '+37529' . rand(1111200, 9999999);
							$user->last_login = Date::now()->subHours(rand(1, 48));
						}

						$user->save();

						if (!$user->isValid()) {
							$errors = $user->getErrors();
							foreach ($errors as $req => $error) {
								throw new \Exception($error, 1);
							}
						}

						try {
							$user->attachRole($role);
						} catch (\Exception $e) {
							var_dump($user->toArray());
							print_r($e->getErrors());
							die($e->getMessage() . "\n");
						}

						$cardType = $faker->randomElement(['Visa', 'MasterCard']);

						switch ($key) {
							case 'customer':
								$entity = factory(User\Customer::class)->create([
									'id' => $user->id,
									'notes' => $faker->text(128),
									'current_city' => $cities->random()->id,
									'card_number' => $faker->creditCardNumber($cardType),
									'card_type' => $cardType,
									'card_name' => $faker->firstName . ' ' . $faker->lastName,
									'card_expiry' => $faker->creditCardExpirationDate,
									'card_cvc' => rand(101, 999),
								]);
								break;

							case 'carrier':
								$entity = factory(User\Carrier::class)->create([
									'id' => $user->id,
									'is_online' => $user->is_enabled === true ? rand(1, 5) === 5 ? false : true : false,
									'birthday' => $faker->date(),
									'id_number' => strtoupper($faker->bothify("## ???##???")),
									'current_city' => $cities->random()->id,
									'default_address' => $faker->address,
									'notes' => $faker->text(128),
								]);
								break;
						}

						if (isset($entity)) {
							if (!$entity->isValid()) {
								$errors = $entity->getErrors()->messages();
								foreach ($errors as $req => $error) {
									foreach ($error as $text) {
										throw new \App\Exceptions\ModelValidationException($text, 1);
									}
								}
							}
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
						'name' => $faker->firstName . ' ' . $faker->lastName,
						'email' => $key . '@barq.com',
						'phone' => $faker->phoneNumber,
						'password' => $this->pwd,
						'is_enabled' => rand(1, 3) === 3 ? false : true,
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
