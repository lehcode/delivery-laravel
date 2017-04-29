<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToUserLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_language', function(Blueprint $table)
        {
            $table->foreign('language_id', 'fk_user_language_languages1')->references('id')->on('languages')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_language_users1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_language', function(Blueprint $table)
        {
            $table->dropForeign('fk_user_language_languages1');
            $table->dropForeign('fk_user_language_users1');
        });
    }
}
