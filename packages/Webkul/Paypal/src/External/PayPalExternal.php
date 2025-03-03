<?php

namespace App\ExternalInquiry\IPartner;

use App\Models\SupplierResponsibleUser;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;

class PayPalExternal
{
    const REQUEST_TIMEOUT = 60;

    const ENVIROMENT = 'sandox';

    /** @var PendingRequest */
    protected $request;

    /** @var string */
    protected $accessToken;

    /** @var bool */
    protected $successful = null;

    /** @var int */
    protected $status = null;

    /** @var array */
    protected $payload = null;

    /** @var string */
    protected $exceptionClass = null;

    /** @var string */
    protected $exceptionMessage = null;

    /**
     * IPartnerUserInquiry constructor.
     */
    public function __construct()
    {
        $this->accessToken = config('app.ipartner_access_token');
        $this->request = Http::baseUrl(config('app.ipartner_base_url') . '/api')
            ->withToken($this->accessToken);
    }

    private function fetchSanboxAccessToken()
    {
        $request = Http::baseUrl('https://api-m.sandbox.paypal.com/');
        $response = $request->contentType("application/x-www-form-urlencoded")
            ->withToken($this->authorizationString(), 'Basic')
            ->post('v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ])->object();

            return [
                'token'      => $response->access_token,
                'tokenType'  => $response->token_type,
                'expiresIn'  => $response->expires_in,
                'createDate' => time(),
            ];

    }

    public function authorizationString()
    {
        return base64_encode('' . ":" . '');
    }

    /**
     * @param bool $successful
     * @return $this
     */
    public function setSuccessful($successful)
    {
        $this->successful = $successful;
        return $this;
    }

    /**
     * @param int $successful
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param array $payload
     * @return $this
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param string $exceptionClass
     * @return $this
     */
    public function setExceptionClass($exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;
        return $this;
    }

    /**
     * @param string $exceptionMessage
     * @return $this
     */
    public function setExceptionMessage($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /**
     * @return bool
     */
    public function ok()
    {
        return $this->successful;
    }

    /**
     * @return bool
     */
    public function fail()
    {
        return !$this->ok();
    }

    /**
     * @return mixed
     */
    public function errors()
    {
        if ($this->ok()) {
            return null;
        }
        return [
            'status'           => $this->status,
            'payload'          => $this->payload,
            'exceptionClass'   => $this->exceptionClass,
            'exceptionMessage' => $this->exceptionMessage
        ];
    }

}
