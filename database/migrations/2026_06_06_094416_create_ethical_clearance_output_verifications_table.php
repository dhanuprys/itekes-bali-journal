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
        Schema::create('ethical_clearance_output_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_output_id')
                ->constrained(
                    table: 'ethical_clearance_outputs',
                    indexName: 'fk_ec_output_verifications_output_id'
                )
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->string('status', 30); // 'approved', 'rejected'
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['ethical_clearance_output_id', 'user_id'],
                'ec_output_verification_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethical_clearance_output_verifications');
    }
};
