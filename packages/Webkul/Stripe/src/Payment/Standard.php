<?php

namespace Webkul\Stripe\Payment;

class Standard extends Stripe
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'stripe_standard';

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
        return route('stripe.standard.redirect');
    }

    /**
     * Return paypal IPN url.
     *
     * @return string
     */
    public function getIPNUrl()
    {
        return $this->getConfigData('sandbox')
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';
    }

     /**
     * Create checkout from cart.
     *
     * @return array
     */
    public function createFromCart()
    {
        $cart = $this->getCart();

        $stripeCustomer =  $this->getBillableInstanceForAuthenticatedCustomer();

        return $stripeCustomer
            ->allowPromotionCodes()
            ->checkout(
            $this->formatCartItems($cart->items),
                [
                    'customer_update' => [
                        'shipping'=>'auto',
                    ],
                    'shipping_address_collection' => [
                        'allowed_countries' => ['GB','US','CA','AU', 'JP', 'MM'],
                    ],
                    'success_url' => route('stripe.standard.success').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('stripe.standard.cancel'),
                    'metadata' => [
                        'customer_id' => $cart->customer_id,
                        'cart_id' => $cart->id,
                    ],
                ],
        );
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
            'return'          => route('stripe.standard.success'),
            'cancel_return'   => route('stripe.standard.cancel'),
            'notify_url'      => route('stripe.standard.ipn'),
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
     * Return cart items.
     *
     * @param \Webkul\Checkout\Contracts\Cart  $cart
     * @return array
     */
    protected function formatCartItems($cart)
    {
        $cartItems['price_data'] = [];

        foreach ($cart->items as $item) {
            $lineItems['price_data'] = [
                'currency'    => $this->currencyToUse(),
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

        return $cartItems;
    }

     /**
     * Return cart items.
     *
     * @param  string  $cart
     * @return array
     */
    protected function currencyToUse() {
        $acceptedCurrency = core()->getConfigData('sales.payment_methods.stripe_smart_button.accepted_currencies');

        $currentCurrency = core()->getCurrentCurrencyCode();

        $acceptedCurrenciesArray = array_map('trim', explode(',', $acceptedCurrency));

        $currencyToUse = in_array($currentCurrency, $acceptedCurrenciesArray)
            ? $currentCurrency
            : $acceptedCurrenciesArray[0];

        return $currencyToUse;
    }
}
