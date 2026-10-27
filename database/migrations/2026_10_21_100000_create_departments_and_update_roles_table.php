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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('university_id')->constrained('departments')->nullOnDelete();
            $table->string('field_of_study')->nullable()->after('name');
            $table->text('description')->nullable()->after('field_of_study');
            $table->text('requirements')->nullable()->after('description');
            $table->string('status')->default('open')->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['field_of_study', 'description', 'requirements', 'status']);
        });

        Schema::dropIfExists('departments');
    }
};
