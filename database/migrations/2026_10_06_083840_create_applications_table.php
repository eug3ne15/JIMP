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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('internship_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, info_requested, rejected, shortlisted
            $table->string('document_path')->nullable();
            $table->text('hr_comment')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_qualified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
