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
        Schema::table('research_submission_details', function (Blueprint $table) {
            $table->renameColumn('final_report_path', 'progress_report_path');
        });

        Schema::table('research_submission_details', function (Blueprint $table) {
            $table->string('final_report_path')->nullable()->after('manuscript_path');
        });

        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->renameColumn('final_report_path', 'progress_report_path');
        });

        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->string('final_report_path')->nullable()->after('manuscript_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->dropColumn('final_report_path');
        });

        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->renameColumn('progress_report_path', 'final_report_path');
        });

        Schema::table('research_submission_details', function (Blueprint $table) {
            $table->dropColumn('final_report_path');
        });

        Schema::table('research_submission_details', function (Blueprint $table) {
            $table->renameColumn('progress_report_path', 'final_report_path');
        });
    }
};
