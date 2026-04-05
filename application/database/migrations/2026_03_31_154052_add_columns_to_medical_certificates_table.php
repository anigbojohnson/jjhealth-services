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
            //
        Schema::table('medical_certificate', function (Blueprint $table) {
            $table->string('fileUpload')->nullable()->after('id');
            $table->string('days_type')->nullable()->after('fileUpload');
            $table->string('singleDay')->nullable()->after('days_type');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_certificates', function (Blueprint $table) {
            $table->dropColumn(['fileUpload', 'days_type', 'singleDay']);

        });
    }
};
