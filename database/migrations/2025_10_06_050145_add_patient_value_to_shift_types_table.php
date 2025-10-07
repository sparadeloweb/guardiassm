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
        if (Schema::hasColumn('shift_types', 'patient_value')) return;

        Schema::table('shift_types', function (Blueprint $table) {
            $table->decimal('patient_value', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_types', function (Blueprint $table) {
            $table->dropColumn('patient_value');
        });
    }
};
