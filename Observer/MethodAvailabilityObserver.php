<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.com/)
 */

namespace Spotii\Spotiipay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;

/**
 * Class MethodAvailabilityObserver
 * @package Spotii\Spotiipay\Observer
 */
class MethodAvailabilityObserver implements ObserverInterface
{
    const PAYMENT_CODE = 'spotiipay';
    private $spotiiApiIdentity;
    private $logger;
    /**
     * MethodAvailabilityObserver constructor.
     * @param SpotiiApiConfigInterface $spotiiApiIdentity
     * @param Logger $logger
     */
    public function __construct(
        SpotiiApiConfigInterface $spotiiApiIdentity,
        Logger $logger
    ) {
        $this->spotiiApiIdentity = $spotiiApiIdentity;
        $this->logger = $logger;
    }

    /**
     * Hide the method if merchant id, public key & private key are not present
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        $methodInstance = $observer->getEvent()->getMethodInstance();

        $merchantId = $this->spotiiApiIdentity->getMerchantId();
        $publicKey = $this->spotiiApiIdentity->getPublicKey();
        $privateKey = $this->spotiiApiIdentity->getPrivateKey();

        if (($methodInstance->getCode() == self::PAYMENT_CODE)
            && (!$merchantId || !$publicKey || !$privateKey)) {
            $result->setData('is_available', false);
        }
    }
}
