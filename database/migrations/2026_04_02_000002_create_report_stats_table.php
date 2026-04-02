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
        Schema::create('t_report_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('t_reports')->onDelete('cascade');
            $table->string('label');
            $table->string('value');
            $table->text('delta_text')->nullable();
            $table->enum('status_level', ['danger', 'warning', 'ok'])->default('ok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_report_stats');
    }
};
