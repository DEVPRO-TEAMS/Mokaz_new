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
        Schema::create('appartements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable();
            $table->string('property_uuid')->nullable();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
             $table->longText('description')->nullable();

             $table->string('type_uuid')->nullable();
            $table->string('commodity_uuid')->nullable();
            $table->integer('nbr_room')->default(0)->nullable();
            $table->integer('nbr_bathroom')->default(0)->nullable();
            $table->integer('nbr_available')->default(0)->nullable();
            
            $table->string('video_url')->nullable();
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
        Schema::dropIfExists('appartements');
    }
};
