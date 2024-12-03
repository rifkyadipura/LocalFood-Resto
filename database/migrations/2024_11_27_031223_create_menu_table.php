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
            $table->id(); // Primary key (auto-increment)
            $table->string('name', 255); // Nama menu
            $table->integer('stok'); // Stok menu
            $table->boolean('status')->default(true); // Status menu (true = tersedia, false = tidak tersedia)
            $table->string('foto')->nullable(); // Foto menu (path file gambar)
            $table->text('deskripsi')->nullable(); // Deskripsi menu
            $table->decimal('harga', 10, 2)->default(0); // Harga menu (2 desimal untuk representasi uang)
            $table->timestamps(); // Kolom created_at dan updated_at
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
