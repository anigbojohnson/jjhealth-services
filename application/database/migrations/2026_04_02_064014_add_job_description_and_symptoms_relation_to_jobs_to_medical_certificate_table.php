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
        Schema::table('medical_certificate', function (Blueprint $table) {
            $table->text('jobDescription')->nullable()->after('id');
            $table->text('symptomsRelationToJobs')->nullable()->after('jobDescription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_certificate', function (Blueprint $table) {
            $table->dropColumn(['jobDescription', 'symptomsRelationToJobs']);
        });
    }
};
