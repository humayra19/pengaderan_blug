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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('nim')->nullable()->after('phone');
            $table->string('study_program')->nullable()->after('nim');
            $table->year('batch_year')->nullable()->after('study_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'nim', 'study_program', 'batch_year']);
        });
    }
};

