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
        Schema::create('partnership_requests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('property_type', ['residential', 'commercial', 'industrial', 'land', 'mixed'])->nullable();
            $table->string('activity_zone')->nullable();
            $table->enum('experience', ['0-2', '3-5', '6-10', '10+'])->nullable();
            $table->enum('portfolio_size', ['1-10', '11-50', '51-100', '100+'])->nullable();
            $table->string('website')->nullable();
            $table->text('message')->nullable();
            $table->boolean('accepts_newsletter')->default(false);

            $table->string('etat')->nullable()->default('actif');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_requests');
    }
};
