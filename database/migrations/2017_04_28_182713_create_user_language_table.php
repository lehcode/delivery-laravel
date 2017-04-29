<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_language', function(Blueprint $table)
        {
            $table->uuid('user_id')->index('fk_user_language_users1_idx');
            $table->unsignedInteger('language_id')->index('fk_user_language_languages1_idx');
            $table->primary(['user_id','language_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_language');
    }
}
