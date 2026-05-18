<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add category to submissions
        Schema::table('ethical_clearance_submissions', function (Blueprint $table) {
            $table->string('category', 30)->after('user_id')->default('clinical');
        });

        // 2. Simplify details table — remove form-based text fields, keep as versioned container
        Schema::table('ethical_clearance_details', function (Blueprint $table) {
            $columns = [
                'is_multicenter',
                'research_title',
                'leader_name',
                'research_location',
                'institution_details',
                'ethical_clearance_subject_id',
                'duration_per_participant',
                'proposal_summary',
                'ethical_issues',
                'ethical_mitigation',
                'experimental_procedure',
                'potential_hazards',
                'previous_experience',
                'documentation_method',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ethical_clearance_details', $column)) {
                    if ($column === 'ethical_clearance_subject_id') {
                        $table->dropForeign(['ethical_clearance_subject_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });

        // 3. Create detail files table for individual template uploads
        Schema::create('ethical_clearance_detail_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_detail_id')
                ->constrained(table: 'ethical_clearance_details', indexName: 'fk_ec_detail_files_detail_id')
                ->cascadeOnDelete();
            $table->string('template_key', 100); // e.g. 'surat_pengantar_mahasiswa'
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamps();
        });

        // 4. Add document columns to outputs
        Schema::table('ethical_clearance_outputs', function (Blueprint $table) {
            $table->string('document_path')->nullable()->after('user_id');
            $table->text('notes')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('ethical_clearance_outputs', function (Blueprint $table) {
            $table->dropColumn(['document_path', 'notes']);
        });

        Schema::dropIfExists('ethical_clearance_detail_files');

        // Re-add the old columns (simplified — won't perfectly restore)
        Schema::table('ethical_clearance_details', function (Blueprint $table) {
            $table->boolean('is_multicenter')->nullable();
            $table->text('research_title')->nullable();
            $table->string('leader_name')->nullable();
            $table->string('research_location')->nullable();
            $table->string('institution_details')->nullable();
            $table->unsignedBigInteger('ethical_clearance_subject_id')->nullable();
            $table->string('duration_per_participant')->nullable();
            $table->text('proposal_summary')->nullable();
            $table->text('ethical_issues')->nullable();
            $table->text('ethical_mitigation')->nullable();
            $table->text('experimental_procedure')->nullable();
            $table->text('potential_hazards')->nullable();
            $table->text('previous_experience')->nullable();
            $table->text('documentation_method')->nullable();
        });

        Schema::table('ethical_clearance_submissions', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
