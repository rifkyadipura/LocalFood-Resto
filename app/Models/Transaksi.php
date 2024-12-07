<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'menu_id',
        'jumlah',
        'total_harga',
        'uang_dibayar',
        'uang_kembalian',
        'metode_pembayaran',
    ];

    public static function generateKodeTransaksi()
    {
        $tanggalHariIni = date('Y-m-d');

        $jumlahTransaksiUnikHariIni = self::whereDate('created_at', $tanggalHariIni)
            ->distinct('kode_transaksi')
            ->count('kode_transaksi');

        $nomorUrut = $jumlahTransaksiUnikHariIni + 1;
        $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . $nomorUrut;

        return $kodeTransaksi;
    }

    /**
     * Relasi ke model Menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
