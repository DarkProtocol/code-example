<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('User ID');
            $table->string('email')->unique()->comment('User email');
            $table->string('nickname', 30)->unique()->comment('User nickname');
            $table->string('password')->comment('Hashed password');
            $table->string('seed_phrase')->nullable()->comment('Hashed seed phrase');
            $table->uuid('ref_id')->nullable()->comment('Referer id');
            $table->string('ref_level')->nullable()->comment('Referral level');
            $table->unsignedTinyInteger('role_id')->default(0)->comment('User role ID');
            $table->string('create_ip')->nullable()->comment('Creation IP');
            $table->string('create_country', 2)->nullable()->comment('Creation country code');
            $table->text('create_ua')->nullable()->comment('Creation User-Agent');
            $table->boolean('is_banned')->default(false)->comment('Is user banned');
            $table->timestamp('activated_at')->nullable()->comment('Account activation date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
