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
        if (Schema::hasColumn('shift_types', 'deleted_at')) return;

        Schema::table('shift_types', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::table('shift_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
