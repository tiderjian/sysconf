<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Encore\Admin\Sysconf\Models\SysconfGroup;

class CreateSysconfGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionModel = config('admin.database.permissions_model');

        Schema::table((new $permissionModel())->getTable(), function(Blueprint $table){
            $table->unique('slug');
        });

        Schema::create('sysconf_groups', function (Blueprint $table) use($permissionModel) {
            $table->increments('id');
            $table->string('title', 30);
            $table->string('permission', 50)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign("permission")->references('slug')->on((new $permissionModel())->getTable());
        });

        SysconfGroup::create([
            'title' => 'general'
        ]);
        SysconfGroup::create([
            'title' => 'system'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionModel = config('admin.database.permissions_model');
        Schema::dropIfExists('sysconf_groups');
        Schema::table((new $permissionModel())->getTable(), function(Blueprint $table){
            $table->dropUnique("admin_permissions_slug_unique");
        });
    }
}
