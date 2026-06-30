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
        Schema::create('community_service_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_service_subdetail_id')->constrained(table: 'community_service_submission_details', indexName: 'fk_cs_members_subdetail_id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_service_members');
    }
};
