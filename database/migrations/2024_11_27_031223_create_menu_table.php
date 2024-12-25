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
            $table->id('menu_id');
            $table->string('nama_menu', 255);
            $table->integer('stok');
            $table->boolean('status')->default(true);
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2)->default(0);
            $table->unsignedBigInteger('kategory_id')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->unsignedBigInteger('diperbarui_oleh')->nullable();
            $table->timestamps();

            // Tambahkan foreign key
            $table->foreign('kategory_id')->references('kategory_id')->on('kategory')->onDelete('set null');
            $table->foreign('dibuat_oleh')->references('users_id')->on('users')->onDelete('set null');
            $table->foreign('diperbarui_oleh')->references('users_id')->on('users')->onDelete('set null');
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
