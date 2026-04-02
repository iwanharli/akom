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
        Schema::create('chart_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('report_sections')->onDelete('cascade');
            $table->enum('chart_type', ['line_oil', 'bar_budget', 'fiscal_stress'])->default('line_oil');
            $table->json('json_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_data');
    }
};
