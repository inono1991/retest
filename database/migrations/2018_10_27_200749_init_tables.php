<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name',16)->unique()->comment('用户组名称');
                $table->string('level')->default(3)->comment('等级');
                $table->string('describe', 64)->comment('描述信息');
                $table->string('status')->default(0)->index()->comment('是否禁用，0-禁用，1-未禁用');
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name',32)->unique()->comment('权限名称');
                $table->string('describe', 64)->comment('描述信息');
                $table->string('status')->default(0)->index()->comment('是否禁用，0-禁用，1-未禁用');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table){
                $table->unsignedInteger('role_id')->index()->comment('用户组id');
                $table->unsignedInteger('permission_id')->index()->comment('权限id');
            });
        }

        if (! Schema::hasTable('user_role')) {
            Schema::create('user_role', function (Blueprint $table){
                $table->unsignedInteger('user_id')->index()->comment('用户id');
                $table->unsignedInteger('role_id')->index()->comment('用户组id');
            });
        }

        if (! Schema::hasTable('user_permission')) {
            Schema::create('user_permission', function (Blueprint $table){
                $table->unsignedInteger('user_id')->index()->comment('用户id');
                $table->unsignedInteger('role_id')->index()->comment('角色id');
                $table->unsignedInteger('permission_id')->index()->comment('权限id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('user_permission');
    }
}
