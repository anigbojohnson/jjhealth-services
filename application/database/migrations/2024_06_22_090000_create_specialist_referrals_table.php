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
        Schema::create('specialist_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('request_status');
            $table->string('request_reason');
            $table->string('image_uploaded'); // For storing the uploaded file path
            $table->string('file_name')->nullable(); // For storing the uploaded file path
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialist_referrals');
    }
};
