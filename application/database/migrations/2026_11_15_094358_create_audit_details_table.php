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
        Schema::create('audit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')
                ->constrained('audits')
                ->cascadeOnDelete();
            $table->foreignId('checklist_item_id')
                ->constrained('checklist_items')
                ->cascadeOnDelete();
            
            // Data Input Auditor
            $table->tinyInteger('score')->default(1); // 1 = Sesuai, 0 = Ada Temuan
            $table->text('finding_note')->nullable(); // Keterangan temuan manual
            $table->string('finding_photo')->nullable(); // Foto temuan real-time

            // Data Tindak Lanjut PIC
            $table->text('action_plan')->nullable(); // Rencana perbaikan dari PIC
            $table->date('due_date')->nullable(); // Date Action Plan
            $table->string('fix_photo')->nullable(); // Foto hasil perbaikan PIC
            $table->timestamp('fixed_at')->nullable();

            // Status & Verifikasi
            $table->string('status')->default('none'); // none (jika score 1), open, in_progress, waiting_verification, closed
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_details');
    }
};
