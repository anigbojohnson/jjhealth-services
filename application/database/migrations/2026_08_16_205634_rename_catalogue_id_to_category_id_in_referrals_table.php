<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing foreign key
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['catalogue_id']);
        });

        // Rename the column
        Schema::table('referrals', function (Blueprint $table) {
            $table->renameColumn('catalogue_id', 'category_id');
        });

        // Add the foreign key using the new column
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Drop the new foreign key
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Rename back
        Schema::table('referrals', function (Blueprint $table) {
            $table->renameColumn('category_id', 'catalogue_id');
        });

        // Restore the original foreign key
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign('catalogue_id')
                ->references('id')
                ->on('catalogues')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }
};