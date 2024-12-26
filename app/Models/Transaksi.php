<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    protected $fillable = [
        'kode_transaksi',
        'menu_id',
        'users_id',
        'jumlah',
        'total_harga',
        'uang_dibayar',
        'uang_kembalian',
        'metode_pembayaran',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

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
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }

    /**
     * Relasi ke model Users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }
}
