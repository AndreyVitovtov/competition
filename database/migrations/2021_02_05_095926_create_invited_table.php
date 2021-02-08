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
            $table->string('referrer');
            $table->string('referral');

            $table->index('add_to_group_id');

            $table->foreign('add_to_group_id')
                ->references('id')->on('add_to_group')
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
