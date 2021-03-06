<?php

use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('posts') )
        {
            Schema::create('posts', function($table)
            {
                $table->engine = 'InnoDB';

                $table->increments('id')->index();
                $table->integer('user_id')->unsigned()->nullable()->default(0);
                $table->integer('status_id')->unsigned()->nullable()->default(0);
                $table->integer('album_id')->unsigned()->nullable()->default(0);
                if (config('cmsharenjoy.language_default')) $table->char('language', 4)->nullable()->index();
                $table->string('type')->index();
                $table->string('title');
                $table->string('img')->nullable();
                $table->string('video')->nullable();
                $table->string('slug')->unique();
                $table->text('content')->nullable();
                $table->integer('sort')->unsigned()->nullable()->default(0);
                $table->date('published_at')->index();
                $table->timestamps();
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
        Schema::drop('posts');
    }

}