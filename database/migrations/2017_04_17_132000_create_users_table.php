<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

	protected $name = 'users';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->name, function (Blueprint $table) {
			$table->uuid('id');
			$table->primary('id');
			$table->string('username');
			$table->string('name')->nullable();
			$table->string('email')->unique()->nullable();
			$table->string('phone')->unique()->nullable();
			$table->dateTime('last_login')->nullable();
			$table->string('password');
			$table->boolean('is_enabled')->default(0);
			$table->ipAddress('last_ip')->nullable();
			$table->rememberToken();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("SET FOREIGN_KEY_CHECKS=0");
		Schema::dropIfExists($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
