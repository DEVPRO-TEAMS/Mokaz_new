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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->string('appart_uuid')->nullable();
            $table->string('property_uuid')->nullable();
            $table->string('partner_uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('comment')->nullable();
            $table->string('rating')->nullable();
            $table->string('etat')->nullable()->default('pending');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
