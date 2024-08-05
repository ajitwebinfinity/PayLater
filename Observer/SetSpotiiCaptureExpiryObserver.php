<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Spotii\Spotiipay\Helper\Data;
use Spotii\Spotiipay\Model\SpotiiPay;

/**
 * Class SetSpotiiCaptureExpiryObserver
 * @package Spotii\Spotiipay\Observer
 */
class SetSpotiiCaptureExpiryObserver implements ObserverInterface
{
    const PAYMENT_CODE = 'spotiipay';

    /**
     * @var SpotiiPay
    */
    private $spotiiPayModel;

    /**
     * @var Data
    */
    private $spotiiHelper;

    /**
     * @var ManagerInterface
    */
    private $messageManager;

    /**
     * Construct
     *
     * @param SpotiiPay $spotiiPayModel
     * @param Data $spotiiHelper
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        SpotiiPay $spotiiPayModel,
        Data $spotiiHelper,
        ManagerInterface $messageManager
    ) {
        $this->spotiiPayModel = $spotiiPayModel;
        $this->spotiiHelper = $spotiiHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * Set Spotii Capture Expiry for Authorize Only payment action
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $this->spotiiHelper->logSpotiiActions('****PayLater capture time setting start****');
            $order = $observer->getEvent()->getOrder();
            $reference = $order->getPayment()->getAdditionalInformation(SpotiiPay::ADDITIONAL_INFORMATION_KEY_ORDERID);
            $this->spotiiHelper->logSpotiiActions("PayLater Reference : $reference");
            $paymentAction = $order->getPayment()->getAdditionalInformation('payment_type');
            $this->spotiiHelper->logSpotiiActions("Payment Type : $paymentAction");
            switch ($paymentAction) {
                case \Spotii\Spotiipay\Model\SpotiiPay::ACTION_AUTHORIZE:
                    $this->spotiiPayModel->setSpotiiCaptureExpiry($reference, $order->getPayment());
                    $this->spotiiHelper->logSpotiiActions('****PayLater capture time setting end****');
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions('Unable to set capture time : ' . $e->getMessage());
            $this->messageManager->addExceptionMessage(
                $e,
                __('Unable to set capture time.')
            );
        }
    }
}
