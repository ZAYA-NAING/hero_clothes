<?php

namespace Webkul\Stripe\Payment;

use Illuminate\Support\Facades\Storage;
use Stripe\StripeClient;
use Laravel\Cashier\Cashier;
use Webkul\Payment\Payment\Payment;

abstract class Stripe extends Payment
{
    /**
     * Stripe publishable key.
     *
     * @var string
     */
    protected $stripePublishableKey;

    /**
     * stripe api secret.
     *
     * @var string
     */
    protected $stripeSecret;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Creat customer and a new stripe billable instance
     *
     * @return \Laravel\Cashier\Billable|null
     */
    public function getBillableInstanceForAuthenticatedCustomer()
    {
        if (! auth()->guard('customer')->check()) {
            return redirect()->route('shop.customer.session.index');
        }

        $customer = auth()->guard('customer')->user();

        $stripeCustomer = ! $customer->stripe_id
            ? $customer->createOrGetStripeCustomer()
            : Cashier::findBillable($customer->stripe_id);

        return $stripeCustomer;
    }

    /**
     * Returns Stripe client instance with stripe secrect key of environment.
     * Use this instance to invoke Stripe APIs, provided the
     * credentials have access.
     *
     * @return StripeClient
     */
    public function client()
    {
        return new StripeClient($this->stripeSecret);
    }

    /**
     * Retrieves the token with the given ID.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \Stripe\Customer
     */
    public function retrieveStripeCustomer($id, $opts = null)
    {
        return $this->client()->customers->retrieve($id, $opts);
    }

    /**
     * Create customer for the payment of client.
     *
     * @param null|array $data
     * @param null|array|string $opts
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \Stripe\Customer the created resource
     */
    public function createStripeCustomer($data = null, $opts = null)
    {
        return $this->client()->customers->create($data, $opts);
    }

    /**
     * Create token for the payment card of client.
     *
     * @param null|array $data
     * @param null|array|string $opts
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \Stripe\Token the created resource
     */
    public function createToken($data = null, $opts = null)
    {
        $card = [
            'card' => [
                'number'    => '424242424242424242',
                'exp_month' => '5',
                'exp_year'  => '2025',
                'cvc'       => '314',
            ],
        ];
        return $this->client()->tokens->create($data, $opts);
    }

    /**
     * Retrieves the token with the given ID.
     *
     * @param array|string $id the ID of the API resource to retrieve, or an options array containing an `id` key
     * @param null|array|string $opts
     *
     * @throws \Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \Stripe\Token
     */
    public function retrieveToken($id, $opts = null)
    {
        return $this->client()->tokens->retrieve($id, $opts);
    }

    /**
     * PayPal web URL generic getter
     *
     * @param  array  $params
     * @return string
     */
    public function getStripeUrl($params = [])
    {
        return sprintf('https://www.%spaypal.com/cgi-bin/webscr%s',
            $this->getConfigData('sandbox') ? 'sandbox.' : '',
            $params ? '?'.http_build_query($params) : ''
        );
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
     * Format a currency value according to paypal's api constraints
     *
     * @param  float|int  $long
     */
    public function formatCurrencyValue($number): float
    {
        return round((float) $number, 2);
    }

    /**
     * Format phone field according to paypal's api constraints
     *
     * Strips non-numbers characters like '+' or ' ' in
     * inputs like "+54 11 3323 2323"
     *
     * @param  mixed  $phone
     */
    public function formatPhone($phone): string
    {
        return preg_replace('/[^0-9]/', '', (string) $phone);
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

    /**
     * Initialize properties.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->stripePublishableKey = $this->getConfigData('stripe_publishable_key') ?: '';

        $this->stripeSecret = $this->getConfigData('stripe_secret') ?: '';
    }
}
