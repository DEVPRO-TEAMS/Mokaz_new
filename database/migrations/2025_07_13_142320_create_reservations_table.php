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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable();
            $table->string('nom')->nullable();
            $table->string('prenoms')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('appart_uuid')->nullable();
            $table->string('sejour')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->integer('nbr_of_sejour')->nullable();

            $table->decimal('total_price', 8, 2)->nullable();
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('payment_amount', 8, 2)->nullable();
            $table->decimal('still_to_pay', 8, 2)->nullable();

            $table->enum('statut_paiement',['pending', 'paid'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'cancelled','reconducted', 'completed'])->default('pending');
            $table->longText('notes')->nullable();
            $table->string('payment_method')->nullable();

            $table->string('traited_by')->nullable();
            $table->dateTime('traited_at')->nullable();

            $table->string('etat')->default('actif')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
