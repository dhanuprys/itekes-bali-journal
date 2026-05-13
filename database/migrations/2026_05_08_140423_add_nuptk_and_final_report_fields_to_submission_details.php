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
            $table->string('leader_nuptk', 50)->nullable()->after('leader_nidn');
            $table->string('supplementary_path')->nullable()->after('manuscript_path');
            $table->text('notes')->nullable()->after('supplementary_path');
        });

        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->string('leader_nuptk', 50)->nullable()->after('leader_nidn');
            $table->string('supplementary_path')->nullable()->after('manuscript_path');
            $table->text('notes')->nullable()->after('supplementary_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_submission_details', function (Blueprint $table) {
            $table->dropColumn(['leader_nuptk', 'supplementary_path', 'notes']);
        });

        Schema::table('community_service_submission_details', function (Blueprint $table) {
            $table->dropColumn(['leader_nuptk', 'supplementary_path', 'notes']);
        });
    }
};
