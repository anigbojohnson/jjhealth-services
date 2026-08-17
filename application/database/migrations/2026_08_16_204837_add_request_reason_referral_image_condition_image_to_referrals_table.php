<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->text('request_reason')->nullable();
            $table->string('referral_image')->nullable();
            $table->string('condition_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn([
                'request_reason',
                'referral_image',
                'condition_image',
            ]);
        });
    }
};