<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('family_name');
            $table->string('email')->unique();
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->date('account_day_of_birth')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_online')->nullable();
            $table->text('bio')->nullable();
            $table->string('gender')->nullable();
            $table->string('profile_image')->default('default-avatar.png');
            $table->integer('rank')->default(0);
            $table->boolean('online')->default(false);
            $table->string('ip_register')->nullable();
            $table->string('ip_current')->nullable();
            $table->string('machine_id')->nullable();
            $table->timestamps();
            $table->dateTimeTz('created_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
