<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();
            $table->string('code', 10)->unique(); // Contoh: A, B, C
            $table->string('name'); // Contoh: POS SECURITY, RESEPSIONIST
            $table->foreignId('pic_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // PIC yang bertanggung jawab
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};