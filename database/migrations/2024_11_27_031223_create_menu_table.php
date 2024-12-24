<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('menu_id'); // Primary key diubah menjadi menu_id
            $table->string('nama_menu', 255); // Mengubah name menjadi nama_menu
            $table->integer('stok'); // Stok menu
            $table->boolean('status')->default(true); // Status menu
            $table->string('foto')->nullable(); // Foto menu
            $table->text('deskripsi')->nullable(); // Deskripsi menu
            $table->decimal('harga', 10, 2)->default(0); // Harga menu
            $table->unsignedBigInteger('kategory_id')->nullable(); // Foreign key ke tabel kategory
            $table->timestamps();

            // Tambahkan foreign key
            $table->foreign('kategory_id')->references('kategory_id')->on('kategory')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
