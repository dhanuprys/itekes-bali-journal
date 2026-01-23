<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ethical_clearance_submission_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_submission_id')->constrained(table: 'ethical_clearance_submissions', indexName: 'fk_ec_logs_submission_id');
            $table->foreignId('user_id')->constrained('users');
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethical_clearance_submission_logs');
    }
};
