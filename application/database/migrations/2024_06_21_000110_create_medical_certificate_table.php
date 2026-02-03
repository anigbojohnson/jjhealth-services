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
        Schema::create('medical_certificate', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column
            $table->date('requestDate');
            $table->string('request_status');

            $table->string('user_email'); // Foreign key column referencing email in users table
            $table->enum('preExistingHealth', ['', 'Yes', 'No']);
            $table->enum('medicationsRegularly', ['', 'Yes', 'No']);
            $table->string('seeking',70);
            $table->string('IAgree')->nullable();
            $table->string('adjustmentsReasons')->nullable();
            $table->text('preExistingHealthConditionInformation')->nullable();
            $table->string('privacy',80);
            $table->text('personCared')->nullable();
            $table->text('careForSomeone')->nullable();
            $table->text('medicationsRegularlyInfo')->nullable();
            $table->text('symptomsDetailed');
            $table->date('validFrom')->nullable();
            $table->string('medicalLetterReasons')->nullable();
            $table->date('symptomsStartDate')->nullable();;
            $table->string('currentStatus')->nullable();
            $table->date('validTo')->nullable();
            $table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_certificate');
    }
};
