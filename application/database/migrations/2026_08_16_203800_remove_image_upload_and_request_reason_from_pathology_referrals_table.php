<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pathology_referrals', function (Blueprint $table) {
            $table->dropColumn([
                'imageUpload',
                'requestReason',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pathology_referrals', function (Blueprint $table) {
            $table->string('imageUpload')->nullable();
            $table->text('requestReason')->nullable();
        });
    }
};