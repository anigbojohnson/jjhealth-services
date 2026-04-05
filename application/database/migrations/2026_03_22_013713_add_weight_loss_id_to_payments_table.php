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
        Schema::table('payments', function (Blueprint $table) {
            //
        $table->unsignedBigInteger('weight_loss_id');
        $table->foreign('weight_loss_id')
            ->references('id')
            ->on('weight_loss') // assuming it links to solutions table
            ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['weight_loss_id']); // drop FK constraint
            $table->dropColumn('weight_loss_id');    // drop column
        });
    }
};
