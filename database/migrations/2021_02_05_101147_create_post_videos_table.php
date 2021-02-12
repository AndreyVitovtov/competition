<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('post_id')->nullable();
            $table->integer('count_likes')->default(0);
            $table->integer('users_id')->unsigned();
            $table->integer('best_videos_id')->unsigned();
            $table->string('video');
            $table->text('text');

            $table->index('users_id');
            $table->index('best_videos_id');

            $table->foreign('users_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('best_videos_id')
                ->references('id')->on('best_videos')
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
        Schema::dropIfExists('post_videos');
    }
}
