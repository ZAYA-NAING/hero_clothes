<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Core\Contracts\PaymentMethod as PaymentMethodContract;
use Webkul\Customer\Models\Customer;

abstract class PaymentMethod extends Model implements PaymentMethodContract
{
    /**
     * Table.
     *
     * @var string
     */
    protected $table = 'customer_payment_methods';

    /**
     * Guarded.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Castable.
     *
     * @var array
     */
    protected $casts = [
        'use_for_shipping' => 'boolean',
        'default_customer_payment_method' => 'boolean',
    ];

    /**
     * Get all the attributes for the attribute groups.
     */
    public function getNameAttribute(): string
    {
        return $this->card_holder_name;
    }

    /**
     * Get the customer record associated with the payment method.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
