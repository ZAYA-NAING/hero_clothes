<?php

namespace Webkul\Stripe\Payment;

use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Cashier;
use Webkul\Payment\Payment\Payment;

class CheckoutSession extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'stripe_checkout_session';

    /**
     * Line items fields mapping.
     *
     * @var array
     */
    protected $itemFieldsFormat = [
        'id'       => 'item_number_%d',
        'name'     => 'item_name_%d',
        'quantity' => 'quantity_%d',
        'price'    => 'amount_%d',
    ];


    /**
     * Return stripe redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('stripe.checkout-session.redirect');
    }

    /**
     * Create checkout from cart.
     *
     * @return array
     */
    public function createFromCart($cart, $order)
    {
        $stripeCustomer = $this->getStripeCustomer();

        return $stripeCustomer->allowPromotionCodes()->checkout(
            $this->formatCartItems($cart->items),
            [
                'customer_update' => [
                    'shipping' => 'auto',
                ],
                'shipping_address_collection' => [
                    'allowed_countries' => ['GB', 'US', 'CA', 'AU', 'JP', 'MM'],
                ],
                'success_url' => route('stripe.checkout-session.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.checkout-session.cancel'),
                'metadata' => [
                    'customer_id' => $cart->customer_id,
                    'cart_id' => $cart->id,
                    'order_id' => $order->id,
                ],
            ],
        );
    }

    /**
     * Return cart items.
     *
     * @param \Webkul\Checkout\Contracts\Cart  $cart
     * @return array
     */
    protected function formatCartItems($cart)
    {
        $lineItems['price_data'] = [];

        foreach ($cart->items as $item) {
            $lineItems['price_data'] = [
                'currency'    => $this->currencyToUse($cart->cart_currency_code),
                'unit_amount' => $this->formatCurrencyValue((float) $item->price),
                'product_data' => [
                    'name' => $item->name,
                    'description' => $item->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS',
                    'metadata' => [
                        'product_id' => $item->product->id,
                    ],
                ],
                'quantity'    => $item->quantity,
            ];
        }

        return $lineItems;
    }

    /**
     * Return currency to use in our application.
     *
     * @param  string  $currencies
     * @return array
     */
    protected function currencyToUse($currencies = 'USD')

    {
        $acceptedCurrency = $currencies === 'USD' ? 'USD' : $currencies;

        $currentCurrency = core()->getCurrentCurrencyCode();

        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));

        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0];

        return $currencyToUse;
    }

    /**
     * Format a currency value according to paypal's api constraints
     *
     * @param  float|int  $long
     */
    public function formatCurrencyValue($number): float
    {
        return round((float) $number, 2);
    }

    /**
     * Creat customer and a new stripe billable instance
     *
     * @return \Laravel\Cashier\Billable|null
     */
    public function getStripeCustomer()
    {
        if (! auth()->guard('customer')->check()) {
            return redirect()->route('shop.customer.session.index');
        }

        $customer = auth()->guard('customer')->user();

        $customer->createOrGetStripeCustomer();

        return Cashier::findBillable($customer->stripe_id);
    }

    /**
     * Return form field array.
     *
     * @return array
     */
    public function getFormFields()
    {
        $cart = $this->getCart();

        $fields = [
            'business'        => $this->getConfigData('business_account'),
            'invoice'         => $cart->id,
            'currency_code'   => $cart->cart_currency_code,
            'paymentaction'   => 'sale',
            'return'          => route('stripe.checkout-session.success'),
            'cancel_return'   => route('stripe.checkout-session.cancel'),
            'charset'         => 'utf-8',
            'item_name'       => core()->getCurrentChannel()->name,
            'amount'          => $cart->sub_total,
            'tax'             => $cart->tax_total,
            'shipping'        => $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0,
            'discount_amount' => $cart->discount_amount,
        ];

        if ($this->getIsLineItemsEnabled()) {
            $fields = array_merge($fields, [
                'cmd'    => '_cart',
                'upload' => 1,
            ]);

            $this->addLineItemsFields($fields);

            if ($cart->selected_shipping_rate) {
                $this->addShippingAsLineItems($fields, $cart->items()->count() + 1);
            }

            if (isset($fields['tax'])) {
                $fields['tax_cart'] = $fields['tax'];
            }

            if (isset($fields['discount_amount'])) {
                $fields['discount_amount_cart'] = $fields['discount_amount'];
            }
        } else {
            $fields = array_merge($fields, [
                'cmd'           => '_ext-enter',
                'redirect_cmd'  => '_xclick',
            ]);
        }

        $this->addAddressFields($fields);

        return $fields;
    }

    /**
     * Add shipping as item.
     *
     * @param  array  $fields
     * @param  int  $i
     * @return void
     */
    protected function addShippingAsLineItems(&$fields, $i)
    {
        $cart = $this->getCart();

        $fields[sprintf('item_number_%d', $i)] = $cart->selected_shipping_rate->carrier_title;
        $fields[sprintf('item_name_%d', $i)] = 'Shipping';
        $fields[sprintf('quantity_%d', $i)] = 1;
        $fields[sprintf('amount_%d', $i)] = $cart->selected_shipping_rate->price;
    }

    /**
     * Add billing address fields
     *
     * @param  array  $fields
     * @return void
     */
    protected function addAddressFields(&$fields)
    {
        $cart = $this->getCart();

        $billingAddress = $cart->billing_address;

        $fields = array_merge($fields, [
            'city'             => $billingAddress->city,
            'country'          => $billingAddress->country,
            'email'            => $billingAddress->email,
            'first_name'       => $billingAddress->first_name,
            'last_name'        => $billingAddress->last_name,
            'zip'              => $billingAddress->postcode,
            'state'            => $billingAddress->state,
            'address'          => $billingAddress->address,
            'address_override' => 1,
        ]);
    }

    /**
     * Checks if line items enabled or not
     *
     * @return bool
     */
    public function getIsLineItemsEnabled()
    {
        return true;
    }

    /**
     * Add order item fields
     *
     * @param  array  $fields
     * @param  int  $i
     * @return void
     */
    protected function addLineItemsFields(&$fields, $i = 1)
    {
        $cartItems = $this->getCartItems();

        foreach ($cartItems as $item) {
            foreach ($this->itemFieldsFormat as $modelField => $paypalField) {
                $fields[sprintf($paypalField, $i)] = $item->{$modelField};
            }

            $i++;
        }
    }

    /**
     * Returns payment method image
     *
     * @return array
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/stripe.png', 'shop');
    }
}
