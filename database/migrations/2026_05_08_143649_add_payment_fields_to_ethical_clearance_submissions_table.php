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
        Schema::table('ethical_clearance_submissions', function (Blueprint $table) {
            $table->boolean('is_student')->default(false)->after('status');
            $table->string('student_nim')->nullable()->after('is_student');
            $table->string('wali_name')->nullable()->after('student_nim');
            $table->string('payment_proof_path')->nullable()->after('wali_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ethical_clearance_submissions', function (Blueprint $table) {
            $table->dropColumn(['is_student', 'student_nim', 'wali_name', 'payment_proof_path']);
        });
    }
};
