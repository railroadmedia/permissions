<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Permissions\Services\ConfigService;

class CreateUserAbilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schemaConnection = Schema::connection(ConfigService::$databaseConnectionName);

        if (!$schemaConnection->hasTable(ConfigService::$tableUserAbilities)) {
            $schemaConnection->create(
                ConfigService::$tableUserAbilities,
                function (Blueprint $table) {
                    $table->increments('id');

                    $table->integer('user_id')->index();
                    $table->integer('ability', 191)->index();

                    $table->dateTime('created_at')->index();
                    $table->dateTime('updated_at')->index();
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ConfigService::$tableUserAbilities);
    }
}
