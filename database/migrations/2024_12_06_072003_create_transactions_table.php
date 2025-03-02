<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->string('transaction_code')->index();
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_price_taxed', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change_amount', 10, 2);
            $table->string('payment_method', 50);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('menu_item_id')->references('menu_item_id')->on('menu_items')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
