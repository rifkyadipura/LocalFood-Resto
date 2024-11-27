<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika nama tabel tidak sesuai dengan konvensi Laravel)
    protected $table = 'menu';

    // Kolom yang dapat diisi menggunakan mass assignment
    protected $fillable = [
        'name',
        'stok',
        'status',
        'foto',
        'deskripsi',
    ];

    // Tipe data kolom tertentu (opsional)
    protected $casts = [
        'status' => 'boolean',
    ];
}
