<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('property_code')->nullable();
            $table->string('property_uuid')->nullable();
            $table->string('appartement_code')->nullable();
            $table->string('appartement_uuid')->nullable();
            $table->longText('query')->nullable();
            $table->timestamps();
        });

        // FULLTEXT index pour recherche rapide
        DB::statement('ALTER TABLE searches ADD FULLTEXT fulltext_query (query)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('searches');
    }
};
