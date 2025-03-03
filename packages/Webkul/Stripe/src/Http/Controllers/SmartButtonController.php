<?php

namespace Webkul\Stripe\Http\Controllers;

use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Webkul\Checkout\Facades\Cart;
use Webkul\Stripe\Payment\SmartButton;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class SmartButtonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SmartButton $smartButton,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
    ) {}

    /**
     * Get Stripe payment methods for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentMethods()
    {
        try {
            $customer = auth()->guard('customer')->user();

            // Check stripe id & create stripe customer
            $stripeCustomer = Cashier::findBillable($customer->stripe_id);

            if (!$stripeCustomer) {
                // Create a Stripe customer for the given model
                $stripeCustomer =  $customer->createOrGetStripeCustomer();
            }

            if ($stripeCustomer) {
                // Determines if the customer currently has at least one payment method of an optional type.
                if ($stripeCustomer->hasPaymentMethod()) {
                    // Get a collection of the customer's payment methods of an optional type.
                    $paymentMethods = $stripeCustomer->paymentMethods();

                    return response()->json($paymentMethods, 200);
                } else {
                    return response()->json(['message' => 'No stripe payment method found'], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Get Stripe payment methods for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hasPaymentMethod()
    {
        try {
            $customer = auth()->guard('customer')->user();

            // Check stripe id & create stripe customer
            $stripeCustomer = Cashier::findBillable($customer->stripe_id);

            if (!$stripeCustomer) {
                // Create a Stripe customer for the given model
                $stripeCustomer =  $customer->createOrGetStripeCustomer();
            }

            if ($stripeCustomer) {
                return response()->json(['has_payment_method' => $stripeCustomer->hasPaymentMethod()], 200);
            }
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Create Stripe payment method for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPaymentMethod()
    {
        $validatedData = $this->validate(request(), [
            'stripe_payment_method' => 'required',
        ]);

        try {
            if ($validatedData['stripe_payment_method']) {
                $customer = auth()->guard('customer')->user();

                // Get the client instance by authenticated Stripe ID
                $stripeCustomer = Cashier::findBillable($customer->stripe_id);
                if (!$stripeCustomer) {
                    // Create a Stripe customer for the given model
                    $stripeCustomer =  $customer->createOrGetStripeCustomer();
                }

                // Check the client's payment method and charge the payment
                if ($stripeCustomer) {
                    // Determines if the customer currently has not a default payment method.
                    if (!$stripeCustomer->hasDefaultPaymentMethod()) {
                        $paymentMethod = $stripeCustomer->updateDefaultPaymentMethod($validatedData['stripe_payment_method']['id']);

                        return response()->json($paymentMethod, 200);
                    } else {
                        $paymentMethod = $stripeCustomer->addPaymentMethod($validatedData['stripe_payment_method']['id']);

                        return response()->json($paymentMethod, 200);
                    }
                }
            };
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Charge the stripe payment for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function charge()
    {
        $validatedData = $this->validate(request(), [
            'payment_method' => 'required',
        ]);

        $data = $this->buildRequestBody();

        // $cart = Cart::getCart();

        try {
            if ($validatedData['payment_method']) {
                $customer = auth()->guard('customer')->user();

                // Get the client instance by authenticated Stripe ID
                $stripeCustomer = Cashier::findBillable($customer->stripe_id);
                if (!$stripeCustomer) {
                    // Create a Stripe customer for the given model
                    $stripeCustomer =  $customer->createOrGetStripeCustomer();
                }
                // Check the client's payment method and charge the payment
                if ($stripeCustomer) {
                    // The payment amount in the lowest denominator of the currency used by your application.
                    // Example: $amount = 100; // $100 in cents. $10 in dollars. $0.10 in euros. $1 in yen. $1000 in won. $1000 in pounds. $1000 in aud. $1000 in nzd. $1000 in sgd. $1000 in rub. $1000 in hkd. $1000 in gbp. $1000 in cad. $1000 in jpy. $1000 in chf. $1000 in mxn. $1000 in myr. $1000 in thb. $1000 in pln. $1000 in nok. $1000 in sek. $1000 in
                    $currency = env('CASHIER_CURRENCY');

                    $amount = $currency == 'usd' ? $data['purchase_units'][0]['amount']['value'] / 100 : $data['purchase_units'][0]['amount']['value'];
                    // Charge the payment of client
                    $payment =  $stripeCustomer->payWith($amount, ['card']);
                    // dd($payment);
                    // $payment =  $stripeCustomer->charge($amount, $validatedData['payment_method']['id']);
                    // TODO "This PaymentIntent is configured to accept payment methods enabled in your Dashboard. Because some of these payment methods might redirect your customer off of your page, you must provide a `return_url`. If you don't want to accept redirect-based payment methods, set `automatic_payment_methods[enabled]` to `true` and `automatic_payment_methods[allow_redirects]` to `never` when creating Setup Intents and Payment Intents."
                    return response()->json($payment, 200);
                }
            };
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Pay the stripe payment for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function payWith()
    {
        $validatedData = $this->validate(request(), [
            'payment_method' => 'required',
        ]);

        $data = $this->buildRequestBody();

        $cart = Cart::getCart();

        try {
            if ($validatedData['payment_method']) {
                $customer = auth()->guard('customer')->user();

                // Get the client instance by authenticated Stripe ID
                $stripeCustomer = Cashier::findBillable($customer->stripe_id);
                if (!$stripeCustomer) {
                    // Create a Stripe customer for the given model
                    $stripeCustomer =  $customer->createOrGetStripeCustomer();
                }
                // Check the client's payment method and charge the payment
                if ($stripeCustomer) {
                    // The payment amount in the lowest denominator of the currency used by your application.
                    // Example: $amount = 100; // $100 in cents. $10 in dollars. $0.10 in euros. $1 in yen. $1000 in won. $1000 in pounds. $1000 in aud. $1000 in nzd. $1000 in sgd. $1000 in rub. $1000 in hkd. $1000 in gbp. $1000 in cad. $1000 in jpy. $1000 in chf. $1000 in mxn. $1000 in myr. $1000 in thb. $1000 in pln. $1000 in nok. $1000 in sek. $1000 in
                    $currency = env('CASHIER_CURRENCY');
                    $amount = $currency == 'usd' ? $data['purchase_units'][0]['amount']['value'] : $data['purchase_units'][0]['amount']['value'] / 100;
                    // $amount =(float) $cart->sub_total + $cart->tax_total + ($cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0) - $cart->discount_amount;

                    // Charge the payment of client
                    $payment =  $stripeCustomer->payWith($amount, ['card']);
                    // TODO "This PaymentIntent is configured to accept payment methods enabled in your Dashboard. Because some of these payment methods might redirect your customer off of your page, you must provide a `return_url`. If you don't want to accept redirect-based payment methods, set `automatic_payment_methods[enabled]` to `true` and `automatic_payment_methods[allow_redirects]` to `never` when creating Setup Intents and Payment Intents."
                    return response()->json($payment, 200);
                }
            };
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Stripe payment intent handling for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePaymentIntent()
    {
        $validatedData = $this->validate(request(), [
            'payment_intent' => 'required',
        ]);

        try {
            if ($validatedData['payment_intent']['status'] === 'succeeded') {
                // Save order to database
                $result = $this->saveOrder();
                return $result;
            }
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Updating the default payment method for authenticated client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDefaultPaymentMethod()
    {
        $validatedData = $this->validate(request(), [
            'payment_method_id' => 'required',
        ]);

        try {
            if ($validatedData['payment_method_id']) {
                $customer = auth()->guard('customer')->user();

                // Get the client instance by authenticated Stripe ID
                $stripeCustomer = Cashier::findBillable($customer->stripe_id);
                if (! $stripeCustomer) {
                    // Create a Stripe customer for the given model
                    $stripeCustomer =  $customer->createOrGetStripeCustomer();
                }

                if ($stripeCustomer) {
                    // Find a PaymentMethod by ID.
                    $paymentMethodId = $stripeCustomer->findPaymentMethod($validatedData['payment_method_id']);
                    // Update customer's default payment method.
                    $setDefaultPaymentMethod = $stripeCustomer->updateDefaultPaymentMethod($paymentMethodId);

                    return response()->json($setDefaultPaymentMethod, 200);
                }
            };
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Create Stripe payment for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPayment()
    {
        try {
            $data = $this->buildRequestBody();
            $amount = $data['purchase_units'][0]['amount'];
            $paymentIntent = $this->smartButton->createPaymentIntent($amount);
            return response()->json($paymentIntent, 200);
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Stripe payment handling for client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePayment()
    {
        try {
            $payment = $this->smartButton->confirmPayment(request()->input('payment_steup_intent.payment_method'));
            if ($payment->status === 'succeeded') {
                // Save order to database
                $order = $this->saveOrder();

                // Redirect to success page
                // return redirect()->route('shop.checkout.onepage.success');
                return $order;
            }
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Paypal order creation for approval of client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder()
    {
        try {
            return response()->json($this->smartButton->createOrder($this->buildRequestBody()));
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Capturing paypal order after approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function captureOrder()
    {
        try {
            $this->smartButton->captureOrder(request()->input('orderData.orderID'));

            return $this->saveOrder();
        } catch (\Exception $e) {
            return response()->json(json_decode($e->getMessage()), 400);
        }
    }

    /**
     * Build request body.
     *
     * @return array
     */
    protected function buildRequestBody()
    {
        $cart = Cart::getCart();

        $billingAddressLines = $this->getAddressLines($cart->billing_address->address);

        $data = [
            'intent' => 'CAPTURE',

            'payer'  => [
                'name' => [
                    'given_name' => $cart->billing_address->first_name,
                    'surname'    => $cart->billing_address->last_name,
                ],

                'address' => [
                    'address_line_1' => current($billingAddressLines),
                    'address_line_2' => last($billingAddressLines),
                    'admin_area_2'   => $cart->billing_address->city,
                    'admin_area_1'   => $cart->billing_address->state,
                    'postal_code'    => $cart->billing_address->postcode,
                    'country_code'   => $cart->billing_address->country,
                ],

                'email_address' => $cart->billing_address->email,
            ],

            'application_context' => [
                'shipping_preference' => 'NO_SHIPPING',
            ],

            'purchase_units' => [
                [
                    'amount'   => [
                        'value'         => $this->smartButton->formatCurrencyValue((float) $cart->sub_total + $cart->tax_total + ($cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0) - $cart->discount_amount),
                        'currency_code' => $cart->cart_currency_code,

                        'breakdown'     => [
                            'item_total' => [
                                'currency_code' => $cart->cart_currency_code,
                                'value'         => $this->smartButton->formatCurrencyValue((float) $cart->sub_total),
                            ],

                            'shipping'   => [
                                'currency_code' => $cart->cart_currency_code,
                                'value'         => $this->smartButton->formatCurrencyValue((float) ($cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0)),
                            ],

                            'tax_total'  => [
                                'currency_code' => $cart->cart_currency_code,
                                'value'         => $this->smartButton->formatCurrencyValue((float) $cart->tax_total),
                            ],

                            'discount'   => [
                                'currency_code' => $cart->cart_currency_code,
                                'value'         => $this->smartButton->formatCurrencyValue((float) $cart->discount_amount),
                            ],
                        ],
                    ],

                    'items'    => $this->getLineItems($cart),
                ],
            ],
        ];

        if (! empty($cart->billing_address->phone)) {
            $data['payer']['phone'] = [
                'phone_type'   => 'MOBILE',

                'phone_number' => [
                    'national_number' => $this->smartButton->formatPhone($cart->billing_address->phone),
                ],
            ];
        }

        if (
            $cart->haveStockableItems()
            && $cart->shipping_address
        ) {
            $data['application_context']['shipping_preference'] = 'SET_PROVIDED_ADDRESS';

            $data['purchase_units'][0] = array_merge($data['purchase_units'][0], [
                'shipping' => [
                    'address' => [
                        'address_line_1' => current($billingAddressLines),
                        'address_line_2' => last($billingAddressLines),
                        'admin_area_2'   => $cart->shipping_address->city,
                        'admin_area_1'   => $cart->shipping_address->state,
                        'postal_code'    => $cart->shipping_address->postcode,
                        'country_code'   => $cart->shipping_address->country,
                    ],
                ],
            ]);
        }

        return $data;
    }

    /**
     * Return cart items.
     *
     * @param  string  $cart
     * @return array
     */
    protected function getLineItems($cart)
    {
        $lineItems = [];

        foreach ($cart->items as $item) {
            $lineItems[] = [
                'unit_amount' => [
                    'currency_code' => $cart->cart_currency_code,
                    'value'         => $this->smartButton->formatCurrencyValue((float) $item->price),
                ],
                'quantity'    => $item->quantity,
                'name'        => $item->name,
                'sku'         => $item->sku,
                'category'    => $item->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS',
            ];
        }

        return $lineItems;
    }

    /**
     * Return convert multiple address lines into 2 address lines.
     *
     * @param  string  $address
     * @return array
     */
    protected function getAddressLines($address)
    {
        $address = explode(PHP_EOL, $address, 2);

        $addressLines = [current($address)];

        if (isset($address[1])) {
            $addressLines[] = str_replace(["\r\n", "\r", "\n"], ' ', last($address));
        } else {
            $addressLines[] = '';
        }

        return $addressLines;
    }

    /**
     * Saving order once captured and all formalities done.
     *
     * @return \Illuminate\Http\Response | array
     */
    protected function saveOrder($isJsonRes = true)
    {
        if (Cart::hasError()) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        try {
            Cart::collectTotals();

            $this->validateOrder();

            $cart = Cart::getCart();

            $data = (new OrderResource($cart))->jsonSerialize();

            $order = $this->orderRepository->create($data);

            $this->orderRepository->update(['status' => 'processing'], $order->id);

            if ($order->canInvoice()) {
                $this->invoiceRepository->create($this->prepareInvoiceData($order));
            }

            Cart::deActivateCart();

            session()->flash('order_id', $order->id);

            if ($isJsonRes) {
                return response()->json([
                    'success' => true,
                ]);
            } else {
                return ['success' => true];
            }
        } catch (\Exception $e) {
            session()->flash('error', trans('shop::app.common.error'));

            throw $e;
        }
    }

    /**
     * Prepares order's invoice data for creation.
     *
     * @param  \Webkul\Sales\Models\Order  $order
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ['order_id' => $order->id];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    protected function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = (float) core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (! Cart::haveMinimumOrderAmount()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->shipping_address
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }
}
