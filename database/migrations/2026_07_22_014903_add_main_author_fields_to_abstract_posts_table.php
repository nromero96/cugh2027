<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainAuthorFieldsToAbstractPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abstract_posts', function (Blueprint $table) {
            $table->json('main_author')
                ->nullable()
                ->after('user_id');

            $table->foreignId('main_author_country_id')
                ->nullable()
                ->after('main_author');

            $table->foreign('main_author_country_id')
                ->references('id')
                ->on('countries')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('abstract_posts', function (Blueprint $table) {
            $table->dropForeign(['main_author_country_id']);

            $table->dropColumn([
                'main_author',
                'main_author_country_id',
            ]);
        });
    }
}
