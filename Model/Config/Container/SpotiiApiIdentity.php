<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Container;

/**
 * Class SpotiiApiIdentity
 * @package Spotii\Spotiipay\Model\Config\Container
 */
class SpotiiApiIdentity extends Container implements SpotiiApiConfigInterface
{
    const XML_PATH_PUBLIC_KEY = 'payment/spotiipay/public_key';
    const XML_PATH_PAYMENT_ACTIVE = 'payment/spotiipay/active';
    const XML_PATH_PAYMENT_MODE = 'payment/spotiipay/payment_mode';
    const XML_PATH_PRIVATE_KEY = 'payment/spotiipay/private_key';
    const XML_PATH_MERCHANT_ID = 'payment/spotiipay/merchant_id';
    const XML_PATH_LOG_TRACKER = 'payment/spotiipay/log_tracker';
    const XML_PATH_PAYMENT_ACTION = 'payment/spotiipay/payment_action';


    private $checkoutUrlLive = "https://api.spotii.com";
    private $checkoutUrlSandbox = "https://api.sandbox.spotii.me";

    private $authUrlLive = "https://auth.spotii.com";
    private $authtUrlSandbox = "https://auth.sandbox.spotii.me";

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getPublicKey()
    {
        return $this->getConfigValue(
            self::XML_PATH_PUBLIC_KEY,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getPrivateKey()
    {
        return $this->getConfigValue(
            self::XML_PATH_PRIVATE_KEY,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMode()
    {
        return $this->getConfigValue(
            self::XML_PATH_PAYMENT_MODE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getMerchantId()
    {
        return $this->getConfigValue(
            self::XML_PATH_MERCHANT_ID,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getSpotiiBaseUrl()
    {
        $paymentMode = $this->getPaymentMode();
        switch ($paymentMode) {
            case 'live':
                return $this->checkoutUrlLive;
                break;
            case 'sandbox':
                return $this->checkoutUrlSandbox;
                break;
            default:
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getSpotiiAuthBaseUrl()
    {
        $paymentMode = $this->getPaymentMode();
        switch ($paymentMode) {
            case 'live':
                return $this->authUrlLive;
                break;
            case 'sandbox':
                return $this->authtUrlSandbox;
                break;
            default:
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function isLogTrackerEnabled()
    {
        return $this->getConfigValue(
            self::XML_PATH_LOG_TRACKER,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getPaymentAction()
    {
        return $this->getConfigValue(
            self::XML_PATH_PAYMENT_ACTION,
            $this->getStore()->getStoreId()
        );
    }
}
