<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategory extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'kategory';

    // Kolom yang bisa diisi (fillable) melalui mass assignment
    protected $fillable = [
        'name',
    ];

    /**
     * Relasi ke tabel `menu` (one-to-many).
     * Satu kategori dapat memiliki banyak menu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategory_id');
    }
}
