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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('code')->nullable();

            $table->string('partner_uuid')->nullable();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();

            $table->string('type_uuid')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();


            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->string('etat')->nullable()->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
