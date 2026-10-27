<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('school_recommendation_path')->nullable()->after('document_path');
            $table->string('insurance_path')->nullable()->after('school_recommendation_path');
            $table->string('cv_path')->nullable()->after('insurance_path');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'school_recommendation_path',
                'insurance_path',
                'cv_path',
            ]);
        });
    }
};
