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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
            
            // $table->enum('source', ['direct', 'seo', 'social', 'ads', 'email'])->nullable();
            // $table->string('referrer')->nullable();

            // $table->string('coordornneGPS', 150)->nullable();
            // $table->string('country', 120)->nullable();
            // $table->string('city', 120)->nullable();

            // $table->timestamp('started_at')->nullable();
            // $table->timestamp('ended_at')->nullable();

            

            // $table->index(['source']);
            // $table->index(['started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
