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
    
        Schema::create('Treatments', function (Blueprint $table) {
 
            $table->id();
            $table->string('user_email');
            $table->string('request_status');
            $table->string('pre_existing_health'); // "Yes" or "No"
            $table->text('information_pre_existing_health')->nullable();
            $table->string('medications_regularly'); // "Yes" or "No"
            $table->text('medications_regularly_info')->nullable();
            $table->date('start_date_symptoms');
            $table->text('detailed_symptoms')->nullable();
            $table->text('treatment_category')->nullable();
            $table->string('payment_status');

            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
