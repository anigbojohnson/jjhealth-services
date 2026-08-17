<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specialist_referrals', function (Blueprint $table) {
            $table->dropColumn([
                'request_reason',
                'image_uploaded',
                'file_name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('specialist_referrals', function (Blueprint $table) {
            $table->text('request_reason')->nullable();
            $table->boolean('image_uploaded')->nullable();
            $table->string('file_name')->nullable();
        });
    }
};