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
        Schema::create('ethical_clearance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_submission_id')->constrained(table: 'ethical_clearance_submissions', indexName: 'fk_ec_details_submission_id');
            $table->boolean('is_multicenter')->nullable();
            $table->text('research_title');
            $table->string('leader_name')->nullable();
            $table->string('research_location')->nullable();
            $table->string('institution_details')->nullable();
            $table->foreignId('ethical_clearance_subject_id')->constrained('ethical_clearance_subjects');
            $table->string('duration_per_participant')->nullable();
            $table->text('proposal_summary')->nullable();
            $table->text('ethical_issues')->nullable();
            $table->text('ethical_mitigation')->nullable();
            $table->text('experimental_procedure')->nullable();
            $table->text('potential_hazards')->nullable();
            $table->text('previous_experience')->nullable();
            $table->text('documentation_method')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethical_clearance_details');
    }
};
