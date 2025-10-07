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
        if (Schema::hasTable('shifts')) return;

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('shift_type_id')->constrained('shift_types');
            $table->decimal('hourly_rate_snapshot', 10, 2);
            $table->decimal('per_patient_rate_snapshot', 10, 2);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('patients_count');
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->integer('total_hours');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
