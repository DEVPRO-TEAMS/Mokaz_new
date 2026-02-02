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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid');
            $table->uuid('visit_uuid');
            $table->string('url')->nullable();
            $table->string('page_type')->nullable(); // home, search, appartement
            $table->uuid('appartement_uuid')->nullable();
            $table->integer('duration')->nullable()->comment('Temps passé en secondes'); 

            $table->timestamps();

            $table->index('visit_uuid');
            $table->index('page_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
