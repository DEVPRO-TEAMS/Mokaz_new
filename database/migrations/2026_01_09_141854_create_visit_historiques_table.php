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
        Schema::create('visit_historiques', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('visit_uuid')->nullable();

            $table->enum('source', ['direct', 'seo', 'social', 'ads', 'email', 'referral', 'organic'])->nullable();
            $table->string('referrer')->nullable();

            $table->string('coordinates')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->nullable(); // en secondes


            $table->timestamps();

            $table->index(['source']);
            $table->index(['started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_historiques');
    }
};
