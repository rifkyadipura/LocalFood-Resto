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
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            // Tambahkan foreign key ke tabel users
            $table->foreign('email')
                ->references('email')
                ->on('users')
                ->onDelete('cascade'); // Menghapus reset password jika pengguna dihapus
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            // Hapus foreign key sebelum drop tabel
            $table->dropForeign(['email']);
        });

        Schema::dropIfExists('password_resets');
    }
};
