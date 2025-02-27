<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika nama tabel tidak sesuai dengan konvensi Laravel)
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';

    // Kolom yang dapat diisi menggunakan mass assignment
    protected $fillable = [
        'nama_menu',
        'stok',
        'status',
        'foto',
        'deskripsi',
        'harga',
        'kategory_id',
        'dibuat_oleh',
        'diperbarui_oleh',
    ];

    // Tipe data kolom tertentu (opsional)
    protected $casts = [
        'status' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategory::class, 'kategory_id', 'kategory_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'users_id');
    }

    public function pengupdate()
    {
        return $this->belongsTo(User::class, 'diperbarui_oleh', 'users_id');
    }
}
