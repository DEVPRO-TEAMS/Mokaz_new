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
        Schema::create('appartement_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('visit_uuid');
            $table->uuid('appartement_uuid');
            $table->timestamp('viewed_at')->useCurrent();

            $table->timestamps();

            $table->index(['appartement_uuid']);
            $table->index(['visit_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartement_views');
    }
};
