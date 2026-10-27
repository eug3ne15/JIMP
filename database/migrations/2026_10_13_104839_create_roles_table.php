<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'intern', 'label' => 'Intern', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'supervisor', 'label' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'hr', 'label' => 'HR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'label' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
