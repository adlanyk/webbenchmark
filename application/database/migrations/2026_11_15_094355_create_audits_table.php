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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number')->unique(); // Contoh: AUD-20261114-001
            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnDelete();
            $table->string('reporter_name');
            $table->date('audit_date');
            $table->decimal('total_score', 4, 2)->default(0.00); // Hasil kalkulasi (misal: 0.79)
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('passed_items')->default(0);
            $table->unsignedInteger('findings_count')->default(0); // Jumlah item bernilai 0
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
