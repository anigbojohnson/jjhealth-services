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
        
        
        Schema::create('payments', function (Blueprint $table) {
            $table->string('payment_id')->primary();
            $table->string('customer_email');
            $table->string('product_id');
            $table->timestamps();
            $table->unsignedBigInteger('mc_id')->nullable(); // Nullable foreign key column
            $table->unsignedBigInteger('specialist_referrals_id')->nullable(); 
            $table->foreign('customer_email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('mc_id')->references('id')->on('medical_certificate')->onDelete('cascade');
            $table->foreign('specialist_referrals_id')->references('id')->on('specialist_referrals')->onDelete('cascade');

            $table->foreign('product_id')->references('solution_id')->on('solutions')->onDelete('cascade');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
        
    }
};
