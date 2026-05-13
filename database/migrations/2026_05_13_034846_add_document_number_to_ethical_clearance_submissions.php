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
            $table->unsignedInteger('document_number')->nullable()->after('status');
            $table->dateTime('document_date')->nullable()->after('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ethical_clearance_submissions', function (Blueprint $table) {
            $table->dropColumn(['document_number', 'document_date']);
        });
    }
};
