<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Permissions\Services\ConfigService;


class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConfigService::$databaseConnectionName)->create(
            ConfigService::$tableUserRoles,
            function(Blueprint $table) {
                $table->increments('id');

                $table->integer('user_id')->index();
                $table->integer('role', 191)->index();

                $table->dateTime('created_at')->index();
                $table->dateTime('updated_at')->index();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConfigService::$tableUserRoles);
    }
}
