<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'menu_id',
        'jumlah',
        'total_harga',
        'uang_dibayar',
        'uang_kembalian',
        'metode_pembayaran',
    ];
}
