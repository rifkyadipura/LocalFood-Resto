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
        Schema::table('menu', function (Blueprint $table) {
            // Menambahkan kolom kategory_id
            $table->unsignedBigInteger('kategory_id')->after('stok')->nullable();

            // Menambahkan foreign key constraint ke tabel kategory
            $table->foreign('kategory_id')->references('id')->on('kategory')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu', function (Blueprint $table) {
            // Menghapus foreign key dan kolom kategory_id
            $table->dropForeign(['kategory_id']);
            $table->dropColumn('kategory_id');
        });
    }
};
