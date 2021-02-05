<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invited', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('add_to_group_id')->unsigned();
            $table->integer('referrer')->unsigned();
            $table->integer('referral')->unsigned();

            $table->index('add_to_group_id');
            $table->index('referrer');
            $table->index('referral');

            $table->foreign('add_to_group_id')
                ->references('id')->on('add_to_group')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('referrer')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('referral')
                ->references('id')->on('users')
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
        Schema::dropIfExists('invited');
    }
}
