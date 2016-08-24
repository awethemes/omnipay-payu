<?php

namespace Omnipay\PayU;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayU\Messages\AccessTokenRequest;
use Omnipay\PayU\Messages\AccessTokenResponse;
use Omnipay\PayU\Messages\CompletePurchaseRequest;
use Omnipay\PayU\Messages\CompletePurchaseResponse;
use Omnipay\PayU\Messages\PurchaseRequest;
use Omnipay\PayU\Messages\PurchaseResponse;

class Gateway extends AbstractGateway
{
    const URL_SANDBOX = 'https://secure.snd.payu.com';
    const URL_PRODUCTION = 'https://secure.payu.com';

    /** @var bool */
    private $isProductionMode;

    /**
     * Get gateway display name
     */
    public function getName()
    {
        return 'PayU';
    }

    /**
     * @return AccessTokenResponse
     */
    public function getAccessToken()
    {
        $request = parent::createRequest(AccessTokenRequest::class, [
            'clientId' => $this->parameters->get('posId'),
            'clientSecret' => $this->parameters->get('clientSecret'),
            'apiUrl' => $this->getApiUrl()
        ]);
        $response = $request->send();
        return $response;
    }

    /**
     * @param array $parameters
     * @return PurchaseResponse
     */
    public function purchase(array $parameters = array())
    {
        $this->setAccessToken($this->getAccessToken()->getAccessToken());
        $request = parent::createRequest(PurchaseRequest::class, $parameters);
        $response = $request->send();

        return $response;
    }

    /**
     * @param array $parameters
     * @return CompletePurchaseResponse
     */
    public function completePurchase(array $parameters = array())
    {
        $this->setAccessToken($this->getAccessToken()->getAccessToken());
        $request = self::createRequest(CompletePurchaseRequest::class, $parameters);
        $response = $request->send();

        return $response;
    }

    /**
     * @return string
     */
    private function getApiUrl()
    {
        if ($this->isProductionMode) {
            return self::URL_PRODUCTION;
        } else {
            return self::URL_SANDBOX;
        }
    }

    public function getDefaultParameters()
    {
        return [
            'posId' => '',
            'secondKey' => '',
            'clientSecret' => '',
            'isProductionMode' => false,
        ];
    }

    /**
     * @param string $secondKey
     */
    public function setSecondKey($secondKey)
    {
        $this->setParameter('secondKey', $secondKey);
    }

    /**
     * @param string $posId
     */
    public function setPosId($posId)
    {
        $this->setParameter('posId', $posId);
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->setParameter('clientSecret', $clientSecret);
    }

    /**
     * @param mixed $isProductionMode
     */
    public function setIsProductionMode($isProductionMode)
    {
        $this->isProductionMode = $isProductionMode;
    }

    private function setApiUrl($apiUrl)
    {
        $this->setParameter('apiUrl', $apiUrl);
    }

    private function setAccessToken($accessToken)
    {
        $this->setParameter('accessToken', $accessToken);
    }

    public function initialize(array $parameters = [])
    {
        parent::initialize($parameters);
        $this->setApiUrl($this->getApiUrl());
        return $this;
    }
}