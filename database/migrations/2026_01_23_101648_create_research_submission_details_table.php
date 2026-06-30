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
        Schema::create('research_submission_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_submission_id')->constrained('research_submissions');
            $table->string('leader_name');
            $table->foreignId('study_program_id')->constrained('study_programs');
            $table->text('title');
            $table->integer('budget')->nullable();
            $table->foreignId('research_target_id')->nullable()->constrained('research_targets');
            $table->string('proposal_path');
            $table->foreignId('research_schema_id')->nullable()->constrained('research_schema');
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
        Schema::dropIfExists('research_submission_details');
    }
};
