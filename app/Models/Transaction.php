<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'transaction_code',
        'menu_item_id',
        'user_id',
        'quantity',
        'total_price',
        'total_price_taxed',
        'amount_paid',
        'change_amount',
        'payment_method',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public static function generateTransactionCode()
    {
        $todayDate = date('Y-m-d');

        $uniqueTransactionsToday = self::whereDate('created_at', $todayDate)
            ->distinct('transaction_code')
            ->count('transaction_code');

        $sequenceNumber = $uniqueTransactionsToday + 1;
        $transactionCode = 'TRX-' . date('Ymd') . '-' . $sequenceNumber;

        return $transactionCode;
    }

    /**
     * Relationship with MenuItem model
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id', 'menu_item_id');
    }

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
