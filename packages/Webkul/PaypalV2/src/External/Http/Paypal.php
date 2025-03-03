<?php

namespace Webkul\PaypalV2\External\Http;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Paypal
{
    /** @var PendingRequest */
    protected $request;

    /** @var PendingRequest */
    protected $tokenRequest;
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
     * Access Token.
     *
     * @var array
     */
    protected $accessToken = [
        'scope'        => null,
        'access_token' => null,
        'token_type'   => null,
        'expires_in'   => null,
        'nonce'        => null,
        'app_id'       => null,
        'create_date'  => null,
    ];
    /**
     * @var string
     */
    private $token = null;
    /**
     * @var string
     */
    private $expires_in = null;
    /**
     * @var string
     */
    private $created_date = null;
    // Hold the class instance.
  private static $instance = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialize();
        $this->tokenRequest = Http::baseUrl($this->baseUrlByEnv() . '/v1/oauth2/token')
            ->withBasicAuth('AdAKmdUEhUSwT_C_PDyuBQdM0DATwQEOGnlM5i1VZ3tO_RvsDE6hn4VEUqgV4c9fdCYs0a3WiTNAnz-R', 'EPThNx-xV-yGzSk4_ZoZSST6XmGV8oX06_1wx4mSdSAvEfocADSTDMHtmCimxzbQIeZIypoxt1R0jeYQ')
            ->withBody('grant_type=client_credentials', "application/x-www-form-urlencoded");
    }

    /**
     * Create PayPal AccessToken.
     *
     * @return mixed
     */
    public function fetchAccessToken()
    {
        // if ($this->hasAccessToken()) {
        //     $tokenResult = [
        //         'access_token' => $this->token,
        //         'expires_in'   => $this->expires_in,
        //         'created_date' => $this->created_date,
        //     ];
        //     return json_encode($tokenResult);
        // }
        try {
            // dd($this->tokenRequest);
            /** @var Response $response */
            $response = $this->tokenRequest->post('/', []);
            dd($response);
            if ($response->successful()) {
                return $response->json();
            } else {
                return false;
            }
        } catch(\Exception $e) {
            Log::error("[fetchAccessToken] An error occurred while fetching access token. error={$e->getMessage()}");
        }


    }

    /**
     * Create PayPal AccessToken.
     *
     * @return object|null
     */
    public function createOrderV1()
    {
        $request = Http::baseUrl($this->baseUrlByEnv() . '/v2/checkout/orders');
        $response = $request->contentType("application/json")
            ->withHeaders([
                'PayPal-Partner-Attribution-Id' =>  '',
                'Prefer' => 'return=minimal'
            ])
            ->withToken($this->accessToken['token']);

        return $response->post('', []);
    }

     /**
     *
     *
     * @return bool
     */
    public function hasAccessToken() {
        return !is_null($this->token);
        // if (is_null($this->token) && $this->isExpired()) {
        //     return false;
        // }
        // return true;
    }

    /**
     * Return access token is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        // return time() >= $this->accessToken['create_date'] + $this->accessToken['expires_in'];
        return time() >= $this->created_date + $this->expires_in;
    }

    /**
     * Return clientId and clientSecret of paypal is encoded with MIME base64
     *

     * @return string
     */
    public function authorizationString()
    {
        $this->clientId = $this->getConfigData('client_id') ?: '';

        $this->clientSecret = $this->getConfigData('client_secret') ?: '';
        return base64_encode($this->clientId . ":" . $this->clientSecret);
    }


    /**
     * Return api base url of paypal
     *

     * @return string
     */
    public function baseUrlByEnv(?string $path = null)
    {
        if (isset($path) && !empty($path)) {
            return $path;
        }
        if ($this->isSandBox()) {
            return 'https://api-m.sanbox.paypal.com';
        }
        // return 'https://api-m.paypal.com';
        return 'https://api-m.sanbox.paypal.com';
    }

    /**
     * Return sandbox or not of paypal from config data
     *
     * @return bool
     */
    public function isSandBox()
    {
        $isSandbox = $this->getConfigData('sandbox') ?: false;
        return $isSandbox;
    }

    /**
     * Retrieve information from payment configuration.
     *
     * @param  string  $field
     * @return mixed
     */
    public function getConfigData($field)
    {
        return core()->getConfigData('sales.payment_methods.code'.'.'.$field);
    }

    /**
     * @param array $result
     * @return $this
     */
    public function setTokenResult($result)
    {
        $this->token = $result['access_token'];
        $this->expires_in = $result['expires_in'];
        $this->created_date = time();
        return $this;
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
