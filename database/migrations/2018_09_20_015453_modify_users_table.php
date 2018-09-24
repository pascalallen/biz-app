<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('state')->nullable()->change();
            $table->string('code')->nullable()->change();
            $table->string('realm_id')->nullable()->change();
            $table->string('refresh_token')->nullable()->change();
            $table->text('access_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('state')->change();
            $table->string('code')->change();
            $table->string('realm_id')->change();
            $table->string('refresh_token')->change();
            $table->text('access_token')->change();
        });
    }
}
