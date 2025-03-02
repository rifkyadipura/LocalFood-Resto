<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    // Kolom yang bisa diisi (fillable) melalui mass assignment
    protected $fillable = [
        'category_name',
    ];

    /**
     * Relasi ke tabel `menu_items` (one-to-many).
     * Satu kategori dapat memiliki banyak menu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'category_id', 'category_id');
    }
}
