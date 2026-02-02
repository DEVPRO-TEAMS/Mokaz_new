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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->string('code')->nullable();
            $table->string('reservation_code')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('paid_sum')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('payment_token')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('command_number')->nullable();
            $table->string('payment_validation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
