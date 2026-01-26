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
        Schema::create('ethical_clearance_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethical_clearance_subdetail_id')->constrained(table: 'ethical_clearance_details', indexName: 'fk_ec_comments_subdetail_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('title')->nullable();
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethical_clearance_comments');
    }
};
