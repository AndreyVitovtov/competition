<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes_videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('post_videos_id')->unsigned();
            $table->integer('user_chat')->unsigned();

            $table->index('post_videos_id');

            $table->foreign('post_videos_id')
                ->references('id')->on('posts_videos')
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
        Schema::dropIfExists('likes_videos');
    }
}
