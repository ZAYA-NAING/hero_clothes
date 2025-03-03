<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Webkul\Core\Models\PaymentMethod;
use Webkul\Customer\Contracts\CustomerPaymentMethod as CustomerPaymentMethodContract;
use Webkul\Customer\Database\Factories\CustomerPaymentMethodFactory;

class CustomerPaymentMethod extends PaymentMethod implements CustomerPaymentMethodContract
{
    use HasFactory;

    /**
     * Define the customer payment type.
     */
    public const PAYMENT_METHOD_TYPE = 'card';

    /**
     * Define the attributes of the customer payment model.
     *
     * @var array default values
     */
    protected $attributes = [
        'customer_payment_method_type' => self::PAYMENT_METHOD_TYPE,
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function boot(): void
    {
        static::addGlobalScope('customer_payment_method_type', static function (Builder $builder) {
            $builder->where('customer_payment_method_type', self::PAYMENT_METHOD_TYPE);
        });

        parent::boot();
    }

    /**
     * Create a new factory instance for the model
     */
    protected static function newFactory(): Factory
    {
        return CustomerPaymentMethodFactory::new();
    }
}
