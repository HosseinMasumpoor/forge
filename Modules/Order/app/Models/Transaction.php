<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\app\Enums\TransactionGateway;
use Modules\Order\app\Enums\TransactionStatus;
use Modules\Order\app\Enums\TransactionType;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'amount',
        'gateway',
        'payment_id',
        'ref_id',
        'rrn',
        'token',
        'status',
        'type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway' => TransactionGateway::class,
            'status' => TransactionStatus::class,
            'type' => TransactionType::class,
        ];
    }

    /**
     * Get the order that owns the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
