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
        Schema::create('reconducted_reservations', function (Blueprint $table) {
            $table->uuid('uuid')->index();
            $table->string('code')->nullable();
            $table->string('original_reservation_uuid')->nullable();
            $table->string('old_appart_uuid')->nullable();
            $table->decimal('old_total_price', 10, 2)->nullable();
            $table->decimal('already_paid', 10, 2)->nullable();

            $table->uuid('new_reservation_uuid')->nullable();
            $table->uuid('new_appart_uuid')->nullable();
            $table->decimal('new_total_price', 10, 2)->nullable();
            $table->decimal('remaining_to_pay', 10, 2)->nullable();
            $table->decimal('amount_to_pay_now', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('etat')->nullable()->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconducted_reservations');
    }
};

