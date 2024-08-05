<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Gateway;

use Spotii\Spotiipay\Helper\Data as SpotiiHelper;
use Spotii\Spotiipay\Model\Api\ConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\ProductWidgetConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;

/**
 * Class Heartbeat
 * @package Spotii\Spotiipay\Model\Gateway
 */
class Heartbeat
{
    /**
     * @var SpotiiApiConfigInterface
     */
    private $spotiiApiConfig;
    /**
     * @var \Spotii\Spotiipay\Model\Api\ProcessorInterface
     */
    private $spotiiApiProcessor;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var ProductWidgetConfigInterface
     */
    private $productWidgetConfig;
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SpotiiHelper
     */
    private $spotiiHelper;

    /**
     * Heartbeat constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param SpotiiHelper $spotiiHelper
     * @param ConfigInterface $config
     * @param \Spotii\Spotiipay\Model\Api\ProcessorInterface $spotiiApiProcessor
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     * @param ProductWidgetConfigInterface $productWidgetConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        SpotiiHelper $spotiiHelper,
        ConfigInterface $config,
        \Spotii\Spotiipay\Model\Api\ProcessorInterface $spotiiApiProcessor,
        SpotiiApiConfigInterface $spotiiApiConfig,
        ProductWidgetConfigInterface $productWidgetConfig
    ) {
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->spotiiHelper = $spotiiHelper;
        $this->config = $config;
        $this->productWidgetConfig = $productWidgetConfig;
        $this->spotiiApiProcessor = $spotiiApiProcessor;
        $this->logger = $logger;
    }

    /**
     * Sending hearbeat to Spotii
     */
    public function send()
    {
        $this->spotiiHelper->logSpotiiActions("****Hearbeat process start****");
        $isPublicKeyEntered = $this->spotiiApiConfig->getPublicKey() ? true : false;
        $isPrivateKeyEntered = $this->spotiiApiConfig->getPrivateKey() ? true : false;
        $isWidgetConfigured = $this->productWidgetConfig->getTargetXPath() ? true : false;
        $isMerchantIdEntered = $this->spotiiApiConfig->getMerchantId() ? true : false;
        $isPaymentMethodActive = $this->spotiiApiConfig->isEnabled() ? true : false;

        $body = [
            'is_payment_active' => $isPaymentMethodActive,
            'is_widget_active' => true,
            'is_widget_configured' => $isWidgetConfigured,
            'is_merchant_id_entered' => $isMerchantIdEntered,
            'merchant_id' => $this->spotiiApiConfig->getMerchantId()
        ];

        if ($isPublicKeyEntered && $isPrivateKeyEntered && $isMerchantIdEntered) {
            $url = $this->spotiiApiConfig->getSpotiiBaseUrl() . '/v1/merchant' . '/magento/heartbeat';
            try {
                $authToken = $this->config->getAuthToken();
                $this->spotiiApiProcessor->call(
                    $url,
                    $authToken,
                    $body,
                    'POST'
                );
                $this->spotiiHelper->logSpotiiActions("****Hearbeat process end****");
            } catch (\Exception $e) {
                $this->spotiiHelper->logSpotiiActions("Error while sending heartbeat to PayLater" . $e->getMessage());
            }
        } else {
            $this->spotiiHelper->logSpotiiActions('Could not send Heartbeat to PayLater. Please set api keys.');
        }
    }
}
