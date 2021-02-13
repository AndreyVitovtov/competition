<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes_photos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('post_photos_id')->unsigned();
            $table->integer('user_chat')->unsigned();

            $table->index('post_photos_id');

            $table->foreign('post_photos_id')
                ->references('id')->on('posts_photos')
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
        Schema::dropIfExists('likes_photos');
    }
}
