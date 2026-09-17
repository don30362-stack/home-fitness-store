<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'purchaser_name',
        'purchaser_phone',
        'purchaser_email',
        'recipient_name',
        'recipient_phone',
        'postal_code',
        'city',
        'district',
        'address',
        'shipping_method',
        'shipping_fee',
        'payment_method',
        'payment_status',
        'order_status',
        'subtotal',
        'total_amount',
        'logistics_company',
        'tracking_number',
    ];

    protected function casts(): array
    {
        return [
            'shipping_fee' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
