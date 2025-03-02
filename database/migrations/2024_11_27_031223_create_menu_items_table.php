<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id('menu_item_id'); // Primary key
            $table->string('menu_name', 255); // Nama menu dalam bahasa Inggris
            $table->integer('stock'); // Stok menu
            $table->boolean('status')->default(true); // Status aktif/tidak
            $table->string('image')->nullable(); // Foto menu (opsional)
            $table->text('description')->nullable(); // Deskripsi menu (opsional)
            $table->decimal('price', 10, 2)->default(0); // Harga menu
            $table->unsignedBigInteger('category_id')->nullable(); // Foreign key ke tabel categories
            $table->unsignedBigInteger('created_by')->nullable(); // User yang membuat menu
            $table->unsignedBigInteger('updated_by')->nullable(); // User yang terakhir mengupdate menu
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
