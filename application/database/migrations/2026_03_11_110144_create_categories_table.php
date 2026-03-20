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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Category name
            $table->string('slug')->unique(); // URL-friendly name
            $table->foreignId('parent_id')->nullable()
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('depth')->default(0); // Level in hierarchy
            $table->timestamps();
            $table->softDeletes(); // Soft deletes
            $table->index(['parent_id', 'depth']); // Fast queries
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
