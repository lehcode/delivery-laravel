<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{

    protected $name = 'countries';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->name, function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('short_name');
            $table->string('alpha2_code', 2);
            $table->string('alpha3_code', 3);
            $table->smallInteger('numeric_code', false, true);
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
        Schema::drop($this->name);
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}
