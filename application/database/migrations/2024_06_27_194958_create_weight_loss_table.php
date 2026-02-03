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
        Schema::create('weight_loss', function (Blueprint $table) {
            $table->id();
            $table->string('user_email'); // Foreign key to reference users table
            $table->string('medication_used');
            $table->string('diseases_pancreas_liver_kidneys');
            $table->string('taking_insulin');
            $table->string('allergic_reaction');
            $table->string('any_allergies');
            $table->string('pregnant');
            $table->string('eating_disorder');
            $table->string('cardiovascular_disease');
            $table->string('strong_pain_killers');
            $table->string('severe_heart_failure');
            $table->string('brain_tumour');
            $table->string('bariatric_surgery');
            $table->string('gastroparesis');
            $table->string('requestReason');
            $table->string('request_status');

            $table->float('height');
            $table->float('weight');
            $table->timestamps();
    
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weight_loss');
    }
};
