<?php

namespace Webkul\Stripe\Payment;

use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;

class SmartButton extends Stripe
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'stripe_smart_button';

    /**
     * Payment intent.
     *
     * @var
     */
    protected $paymentIntent;

    /**
     * Paypal partner attribution id.
     *
     * @var string
     */
    protected $paypalPartnerAttributionId = 'Bagisto_Cart';

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create the payment intent for client.
     *
     * @param  array $amount
     * @return \Stripe\PaymentIntent
     */
    public function createPaymentIntent($amount) {
        $this->paymentIntent = $this->client()->paymentIntents->create([
            'amount'   => $amount['value'], // Amount in cents
            'currency' => $amount['currency_code'],
        ]);

        return $this->paymentIntent;
    }

    /**
     * Confirm the payment for client.
     *
     * @param  string $paymentMethodId
     * @return mixed
     */
    public function confirmPayment($paymentMethodId) {
        $payment = $this->client()->paymentIntents->confirm(
            $this->paymentIntent->id,
            ['payment_method' => $paymentMethodId]
        );
        return $payment;
    }

    /**
     * Create order for approval of client.
     *
     * @param  array  $body
     * @return HttpResponse
     */
    public function createOrder($body)
    {
        $request = new OrdersCreateRequest;
        $request->headers['PayPal-Partner-Attribution-Id'] = $this->paypalPartnerAttributionId;
        $request->prefer('return=representation');
        $request->body = $body;

        return $this->client()->execute($request);
    }

    /**
     * Capture order after approval.
     *
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function captureOrder($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);

        $request->headers['PayPal-Partner-Attribution-Id'] = $this->paypalPartnerAttributionId;
        $request->prefer('return=representation');

        $this->client()->execute($request);
    }

    /**
     * Get order details.
     *
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function getOrder($orderId)
    {
        return $this->client()->execute(new OrdersGetRequest($orderId));
    }

    /**
     * Get capture id.
     *
     * @param  string  $orderId
     * @return string
     */
    public function getCaptureId($orderId)
    {
        $paypalOrderDetails = $this->getOrder($orderId);

        return $paypalOrderDetails->result->purchase_units[0]->payments->captures[0]->id;
    }

    /**
     * Refund order.
     *
     * @return HttpResponse
     */
    public function refundOrder($captureId, $body = [])
    {
        $request = new CapturesRefundRequest($captureId);

        $request->headers['PayPal-Partner-Attribution-Id'] = $this->paypalPartnerAttributionId;
        $request->body = $body;

        return $this->client()->execute($request);
    }

    /**
     * Return paypal redirect url
     *
     * @return string
     */
    public function getRedirectUrl() {}

    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use LiveEnvironment.
     *
     * @return PayPalCheckoutSdk\Core\SandboxEnvironment|PayPalCheckoutSdk\Core\ProductionEnvironment
     */
    protected function environment()
    {
        $isSandbox = $this->getConfigData('sandbox') ?: false;

        if ($isSandbox) {
            return new SandboxEnvironment($this->stripePublishableKey, $this->stripeSecret);
        }

        return new ProductionEnvironment($this->stripePublishableKey, $this->stripeSecret);
    }


}
