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
        if (Schema::hasTable('attention_pathology')) return;

        Schema::create('attention_pathology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attention_id')->constrained('attentions');
            $table->foreignId('pathology_id')->constrained('pathologies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attention_pathology');
    }
};
