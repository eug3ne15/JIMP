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
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('institution')->nullable();
            $table->unique(['user_id', 'course_id'], 'applications_user_course_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique('applications_user_course_unique');
            $table->dropConstrainedForeignId('course_id');
            $table->dropColumn('institution');
        });
    }
};
