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
        Schema::create('pathology_referrals', function (Blueprint $table) {
          $table->id();
            $table->string('user_email');
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');

            $table->string('imageUpload')->nullable();
            $table->json('solution_available_testing');
            $table->string('requestReason');
            $table->string('request_status')->default('new request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathology_referrals');
    }
};
