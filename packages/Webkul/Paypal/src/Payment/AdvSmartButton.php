<?php

namespace Webkul\Paypal\Payment;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;

use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

class AdvSmartButton extends Paypal
{
    /**
     * Client.
     *
     * @var PaypalServerSdkLib\PaypalServerSdkClientBuilder::environment
     */
    protected $client;
    /**
     * Client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'paypal_adv_smart_button';

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
        $this->initialize();
    }

    /**
     * Returns PayPal HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PayPal APIs, provided the
     * credentials have access.
     *
     * @return PaypalServerSdkLib\PaypalServerSdkClientBuilder::environment
     */
    public function client()
    {
        $isSandbox = $this->getConfigData('sandbox') ?: false;
        $this->client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $this->clientId,
                    $this->clientSecret
                )
            )
            ->environment($isSandbox ? Environment::SANDBOX : Environment::PRODUCTION);
        return $this->client;
    }

    public function handleResponse($response)
{
    $jsonResponse = json_decode($response->getBody(), true);
    return [
        "jsonResponse" => $jsonResponse,
        "httpStatusCode" => $response->getStatusCode(),
    ];
}


    /**
     * Create order for approval of client.
     *
     * @param  array  $cart
     * @return HttpResponse
     */
    public function createOrder($cart)
    {
        // $orderBody = [
        //     "body" => OrderRequestBuilder::init($cart['intent'], [
        //         PurchaseUnitRequestBuilder::init(
        //             AmountWithBreakdownBuilder::init($cart['purchase_units'][0]['amount']['currency_code'], $cart['purchase_units'][0]['amount']['value'])->build()
        //         )->build(),
        //     ])->build(),
        // ];

        $orderBody = [
            "body" => OrderRequestBuilder::init($cart['intent'], $cart['purchase_units'])->build(),
        ];

        $apiResponse = $this->client->build()->getOrdersController()->ordersCreate($orderBody);

        return json_decode($apiResponse->getBody(), true);
    }

    /**
     * Capture order after approval.
     *
     * @param  string  $orderId
     * @return mixed
     */
    public function captureOrder($orderID)
    {

        $captureBody = [
            "id" => $orderID,
        ];

        $apiResponse = $this->client->build()->getOrdersController()->ordersCapture($captureBody);

        return json_decode($apiResponse->getBody(), true);
    }

    /**
     * Authorizes payment for an order.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_authorize
     */
    public function authorizeOrder($orderID)
    {
        $authorizeBody = [
            "id" => $orderID,
        ];

        $apiResponse = $this->client()
            ->build()
            ->getOrdersController()
            ->ordersAuthorize($authorizeBody);

        return json_decode($apiResponse->getBody(), true);
    }

    /**
    * Captures an authorized payment, by ID.
    * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_capture
     */
    public function captureAuthorize($authorizationId)
    {
        $captureAuthorizeBody = [
            "authorizationId" => $authorizationId,
        ];

        $apiResponse =  $this->client->build()
            ->getPaymentsController()
            ->authorizationsCapture($captureAuthorizeBody);

        return json_decode($apiResponse->getBody(), true);
    }


    /**
     * Get order details.
     *
     * @param  string  $orderId
     * @return HttpResponse
     */
    public function getOrder($orderId)
    {
        $order = [
            "id" => $orderId,
        ];
        return $this->client->build()->getOrdersController()->ordersGet($order);
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
            return new SandboxEnvironment($this->clientId, $this->clientSecret);
        }

        return new ProductionEnvironment($this->clientId, $this->clientSecret);
    }

    /**
     * Initialize properties.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->clientId = $this->getConfigData('client_id') ?: '';

        $this->clientSecret = $this->getConfigData('client_secret') ?: '';
    }
}
