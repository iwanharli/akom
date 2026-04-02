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
        Schema::create('t_analysis_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('t_report_sections')->onDelete('cascade');
            $table->enum('item_type', ['timeline', 'intel_card', 'fact_list'])->default('intel_card');
            $table->string('tag')->nullable();
            $table->string('heading');
            $table->text('body')->nullable();
            $table->string('event_date')->nullable(); // Flexible string for "Q1 2026" etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_analysis_items');
    }
};
