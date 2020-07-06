<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id',1,20);
			$table->string('username', 191)->nullable()->default(NULL)->unique();
			$table->string('firstname', 64)->nullable()->default(NULL);
			$table->string('lastname', 64)->nullable()->default(NULL);
            $table->string('email',64)->nullable()->default(NULL)->unique();
			$table->string('type', 64)->nullable()->default(NULL)->default('user')->index();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
