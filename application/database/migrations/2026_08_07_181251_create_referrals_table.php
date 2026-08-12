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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->foreignId('catalogue_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->string('request_status')
                  ->default('new request');
            $table->foreign('user_email')
                  ->references('email')
                  ->on('users')
                  ->onDelete('cascade');
            $table->timestamps();
            // Useful for retrieving a user's referrals
            $table->index('user_email');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
