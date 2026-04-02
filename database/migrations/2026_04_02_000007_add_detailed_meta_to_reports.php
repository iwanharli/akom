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
        Schema::table('reports', function (Blueprint $table) {
            $table->text('alert_text')->nullable()->after('subtitle');
            $table->decimal('asumsi_icp', 15, 2)->nullable()->after('brent_oil_price');
            $table->decimal('asumsi_kurs', 15, 2)->nullable()->after('asumsi_icp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['alert_text', 'asumsi_icp', 'asumsi_kurs']);
        });
    }
};
