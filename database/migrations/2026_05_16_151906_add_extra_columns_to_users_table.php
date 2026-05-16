<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing columns if they don't already exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'registration_number')) {
                $table->string('registration_number')->nullable()->unique()->after('phone');
            }
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null')->after('registration_number');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'staff', 'admin'])->default('student')->after('department_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'registration_number', 'department_id', 'role']);
        });
    }
};
