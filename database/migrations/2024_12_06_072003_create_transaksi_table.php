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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_transaksi')->index();
            $table->unsignedBigInteger('menu_id');
            $table->integer('jumlah');
            $table->decimal('total_harga', 10, 2);
            $table->decimal('uang_dibayar', 10, 2);
            $table->decimal('uang_kembalian', 10, 2);
            $table->string('metode_pembayaran', 50);
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
