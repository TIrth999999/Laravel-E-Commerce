<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_name', 'customer_email', 'customer_phone',
        'customer_address', 'subtotal', 'discount', 'tax_amount', 'total',
        'payment_status',
        'payment_gateway',
        'razorpay_order_id', 'razorpay_payment_id',
        'stripe_session_id', 'stripe_payment_intent_id',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
