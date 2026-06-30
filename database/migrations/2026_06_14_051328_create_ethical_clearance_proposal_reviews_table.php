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
        Schema::create('ethical_clearance_proposal_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_submission_id')
                ->constrained(
                    table: 'ethical_clearance_submissions',
                    indexName: 'fk_ec_prop_reviews_sub_id'
                )
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('status'); // 'approved', 'rejected', 'revision_needed'
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['ethical_clearance_submission_id', 'user_id'], 'ec_proposal_review_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethical_clearance_proposal_reviews');
    }
};
