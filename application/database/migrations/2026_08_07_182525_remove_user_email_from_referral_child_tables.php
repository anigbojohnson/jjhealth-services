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
    Schema::table('specialist_referrals', function (Blueprint $table) {
        $table->foreignId('referral_id')
            ->after('id')
            ->constrained('referrals')
            ->cascadeOnDelete();

        $table->dropColumn('user_email');
        $table->dropColumn('request_status');

    });

    Schema::table('pathology_referrals', function (Blueprint $table) {
        $table->foreignId('referral_id')
            ->after('id')
            ->constrained('referrals')
            ->cascadeOnDelete();
        $table->dropColumn('request_status');
        $table->dropColumn('user_email');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialist_referrals', function (Blueprint $table) {
            $table->string('user_email')->nullable();
        });

        Schema::table('pathology_referrals', function (Blueprint $table) {
            $table->string('user_email')->nullable();
        });
    }
};
