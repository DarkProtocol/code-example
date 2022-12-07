<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_comments', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->comment('Comment ID');
            $table->uuid('user_id')->comment('User ID');
            $table->uuid('author_id')->comment('Author ID');
            $table->uuid('reporter_id')->nullable()->comment('Reporter ID');
            $table->text('content')->comment('Comment content');
            $table->text('report_reason')->nullable()->comment('Report reason content');
            $table->boolean('rating')->nullable()->comment('Comment rating (true - positive, false - negative, null - neutral');
            $table->timestamp('reported_at')->nullable()->comment('Report date');
            $table->timestamps();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('reporter_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
        Schema::table('user_comments', function (Blueprint $table) {
            $table->uuid('comment_id')->nullable()->comment('Comment ID (for answers)');
            $table
                ->foreign('comment_id')
                ->references('id')
                ->on('user_comments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_comments');
    }
}
