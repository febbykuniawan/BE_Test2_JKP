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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userIdSeller');
            $table->unsignedBigInteger('userIdCustomer');
            $table->unsignedBigInteger('inventoriesId');
            $table->date('date');
            $table->string('status');
            $table->integer('quantity');
            $table->integer('totalPrice');
            $table->timestamps();

            $table->foreign('userIdSeller')->references('id')->on('users');
            $table->foreign('userIdCustomer')->references('id')->on('users');
            $table->foreign('inventoriesId')->references('id')->on('inventories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
