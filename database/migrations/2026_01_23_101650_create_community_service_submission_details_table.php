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
        Schema::create('community_service_submission_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_service_submission_id')->constrained(table: 'community_service_submissions', indexName: 'fk_cs_details_submission_id');
            $table->string('leader_name');
            $table->foreignId('study_program_id')->constrained('study_programs');
            $table->text('title');
            $table->integer('budget')->nullable();
            $table->foreignId('community_service_target_id')->nullable()->constrained(table: 'community_service_targets', indexName: 'fk_cs_details_target_id');
            $table->string('proposal_path');
            $table->foreignId('community_service_schema_id')->nullable()->constrained(table: 'community_service_schema', indexName: 'fk_cs_details_schema_id');
            $table->string('final_leader_name')->nullable();
            $table->string('leader_nidn', 50)->nullable();
            $table->text('final_title')->nullable();
            $table->string('final_report_path')->nullable();
            $table->string('manuscript_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_service_submission_details');
    }
};
