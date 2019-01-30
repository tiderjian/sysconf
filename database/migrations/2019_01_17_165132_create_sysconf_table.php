<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysconfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sysconfs', function (Blueprint $table) {
            $table->increments('id');
            $table->string("slug", 50)->unique();
            $table->string("type", 30);
            $table->string("title", 50);
            $table->unsignedInteger("group");
            $table->json("extra")->nullable();
            $table->string("tips", 255)->nullable();
            $table->unsignedTinyInteger("status");
            $table->text("value")->nullable();
            $table->unsignedSmallInteger("sort")->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign("group")->references('id')->on('sysconf_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sysconfs');
    }
}
