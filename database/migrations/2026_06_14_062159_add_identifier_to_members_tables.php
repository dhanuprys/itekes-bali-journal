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
        Schema::table('research_members', function (Blueprint $table) {
            $table->string('identifier', 50)->nullable()->after('name')->comment('NIM / NUPTK');
        });

        Schema::table('community_service_members', function (Blueprint $table) {
            $table->string('identifier', 50)->nullable()->after('name')->comment('NIM / NUPTK');
        });

        Schema::table('ethical_clearance_members', function (Blueprint $table) {
            $table->string('identifier', 50)->nullable()->after('name')->comment('NIM / NUPTK');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_members', function (Blueprint $table) {
            $table->dropColumn('identifier');
        });

        Schema::table('community_service_members', function (Blueprint $table) {
            $table->dropColumn('identifier');
        });

        Schema::table('ethical_clearance_members', function (Blueprint $table) {
            $table->dropColumn('identifier');
        });
    }
};
