<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use App\Models\User;
use App\Models\User\Role;
use App\Models\User\Permission;
use App\Repositories\User\UserRepositoryInterface;

class SetupAclTablesSeed extends Seeder
{

    var $pwd = 'Qrab17';

    var $phone = '+375296000000';

    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {

        $faker = Faker\Factory::create('en_GB');

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

            $this->command->info("Creating '{$key}' user");

            // Create default user for each role
            /*$userData = [
                'name' => ucwords(str_replace("_", " ", $key)),
                'email' => $key.'@server.com',
                'password' => bcrypt($this->pwd),
                'remember_token' => str_random(10),
                'phone' => $faker->phoneNumber,
                'last_login' => Date::now()->subDays(rand(0, 4)),
            ];
            $user = User::create($userData);*/

            $user = app()->make(UserRepositoryInterface::class)->create([
                'name' => $faker->name,
                'email' => $key . '@barq.com',
                'phone' => $faker->phoneNumber,
                'password' => $this->pwd,
                'is_enabled' => true,
                'last_login' => Date::now(),
                //'remember_token' => str_random(10),
            ]);
            $user->attachRole($role);
        }

        // creating user with permissions
        if (!empty($userPermission)) {
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
                        //'remember_token' => str_random(10),
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
