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
        Schema::table('solutions', function (Blueprint $table) {

               // Explicitly cast varchar -> bigint
    DB::statement('ALTER TABLE solutions ALTER COLUMN category TYPE bigint USING category::bigint');
    
    // Rename column
    $table->renameColumn('category', 'category_id');
    
    // Add foreign key
    $table->foreign('category_id')
          ->references('id')
          ->on('categories')
          ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solutions', function (Blueprint $table) {
                       // Change it back to bigint if rolling back
            $table->unsignedBigInteger('category_id')->change();

            // Re-add foreign key
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->cascadeOnDelete();
        });
    }
};
