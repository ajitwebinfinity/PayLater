<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Psr\Log\LoggerInterface as Logger;
use Spotii\Spotiipay\Helper\Data as SpotiiHelper;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;

/**
 * Class Config
 * @package Spotii\Spotiipay\Model\Api
 */
class Config implements ConfigInterface
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ScopeConfig
     */
    protected $scopeConfig;
    /**
     * @var SpotiiApiConfigInterface
     */
    protected $spotiiApiIdentity;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var ProcessorInterface
     */
    protected $apiProcessor;

    /**
     * @var SpotiiHelper
     */
    protected $spotiiHelper;

    /**
     * Config constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param ProcessorInterface $apiProcessor
     * @param SpotiiApiConfigInterface $spotiiApiIdentity
     * @param SpotiiHelper $spotiiHelper
     * @param JsonHelper $jsonHelper
     * @param Logger $logger
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        ProcessorInterface $apiProcessor,
        SpotiiApiConfigInterface $spotiiApiIdentity,
        SpotiiHelper $spotiiHelper,
        JsonHelper $jsonHelper,
        Logger $logger,
        ScopeConfig $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->apiProcessor = $apiProcessor;
        $this->spotiiApiIdentity = $spotiiApiIdentity;
        $this->spotiiHelper = $spotiiHelper;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get auth token
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAuthToken()
    {
        $url = $this->spotiiApiIdentity->getSpotiiAuthBaseUrl() . '/api/v1.0/merchant/authentication/';
        $publicKey = $this->spotiiApiIdentity->getPublicKey();
        $privateKey = $this->spotiiApiIdentity->getPrivateKey();
        $body = [
            "public_key" => $publicKey,
            "private_key" => $privateKey
        ];
        try {
            $response = $this->apiProcessor->call(
                $url,
                null,
                $body,
                "POST",
            );
            $body = $this->jsonHelper->jsonDecode($response);
            return $body['token'];
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Gateway error: %1', $e->getMessage())
            );
        }
    }

    /**
     * Get complete url
     * @param $orderId
     * @param $reference
     * @return mixed
     */
    public function getCompleteUrl($orderId, $reference, $quoteId)
    {
        return $this->urlBuilder->getUrl("spotiipay/standard/complete/id/$orderId/magento_spotii_id/$reference/quote_id/$quoteId", ['_secure' => true]);
    }

    /**
     * Get cancel url
     * @return mixed
     */
    public function getCancelUrl($orderId, $reference)
    {
        return $this->urlBuilder->getUrl("spotiipay/standard/cancel/id/$orderId/magento_spotii_id/$reference/submitted/0", ['_secure' => true]);
    }
}
