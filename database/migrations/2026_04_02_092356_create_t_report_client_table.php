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
        Schema::create('t_report_client', function (Blueprint $table) {
            $table->foreignId('report_id')->constrained('t_reports')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('t_clients')->cascadeOnDelete();
            
            $table->primary(['report_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_report_client');
    }
};
