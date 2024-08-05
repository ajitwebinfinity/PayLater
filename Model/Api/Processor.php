<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Psr\Log\LoggerInterface as Logger;
use Spotii\Spotiipay\Helper\Data as SpotiiHelper;

/**
 * Class Processor
 * @package Spotii\Spotiipay\Model\Api
 */
class Processor implements ProcessorInterface
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
     * @var Curl
     */
    protected $curl;

    /**
     * @var SpotiiHelper
     */
    protected $spotiiHelper;

    /**
     * Processor constructor.
     * @param Curl $curl
     * @param SpotiiHelper $spotiiHelper
     * @param JsonHelper $jsonHelper
     * @param Logger $logger
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        Curl $curl,
        SpotiiHelper $spotiiHelper,
        JsonHelper $jsonHelper,
        Logger $logger,
        ScopeConfig $scopeConfig
    ) {
        $this->curl = $curl;
        $this->spotiiHelper = $spotiiHelper;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Call to Spotii Gateway
     *
     * @param $url
     * @param $authToken
     * @param bool $body
     * @param string $method
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function call($url, $authToken = null, $body = false, $method = ZendClient::GET)
    {
        try {
            if ($authToken) {
                $this->curl->addHeader("Authorization", "Bearer $authToken");
            }
            $this->spotiiHelper->logSpotiiActions("Auth token : $authToken");
            $this->spotiiHelper->logSpotiiActions("****Request Info****");
            $requestLog = [
                'type' => 'Request',
                'method' => $method,
                'url' => $url,
                'body' => $body
            ];
            $this->spotiiHelper->logSpotiiActions($requestLog);
            $this->curl->setTimeout(ApiParamsInterface::TIMEOUT);
            $this->curl->addHeader("Content-Type", ApiParamsInterface::CONTENT_TYPE_JSON);
            $this->curl->addHeader("Accept", ApiParamsInterface::CONTENT_TYPE_JSON);
            switch ($method) {
                case 'POST':
                    $this->curl->post($url, $this->jsonHelper->jsonEncode($body));
                    break;
                case 'GET':
                    $this->curl->get($url);
                    break;
                default:
                    break;
            }

            $response = $this->curl->getBody();

            $responseLog = [
                'type' => 'Response',
                'method' => $method,
                'url' => $url,
                'httpStatusCode' => $this->curl->getStatus()
            ];
            $this->spotiiHelper->logSpotiiActions("****Response Info****");
            $this->spotiiHelper->logSpotiiActions($responseLog);
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Gateway error: %1', $e->getMessage())
            );
        }
        return $response;
    }
}
