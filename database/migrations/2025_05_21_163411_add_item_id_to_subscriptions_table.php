<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

     public function up()
{
    Schema::table('subscriptions', function (Blueprint $table) {
        $table->unsignedBigInteger('item_id')->nullable()->after('user_id');
        $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
};
