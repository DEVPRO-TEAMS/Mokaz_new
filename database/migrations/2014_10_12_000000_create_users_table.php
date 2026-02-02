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
            $table->uuid('uuid')->unique();
            $table->uuid('code')->nullable();

            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('password_edit')->nullable();

            $table->string('user_type')->nullable();
            $table->string('partner_uuid')->nullable();

            $table->string('etat')->nullable();

            $table->date('last_login')->nullable();
            $table->boolean('is_logged_in')->default(false);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
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
