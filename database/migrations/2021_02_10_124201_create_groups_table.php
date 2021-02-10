<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('group_id');
            $table->string('group_link');
            $table->integer('add_to_group_id')->unsigned();

            $table->index('add_to_group_id');

            $table->foreign('add_to_group_id')
                ->references('id')->on('add_to_group')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('invited', function (Blueprint $table) {
            $table->integer('groups_id')->unsigned();
            $table->index('groups_id');
            $table->foreign('groups_id')
                ->references('id')->on('groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('groups');
    }
}
