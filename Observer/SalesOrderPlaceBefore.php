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
use Magento\Framework\Message\ManagerInterface;
use Spotii\Spotiipay\Helper\Data;
use Spotii\Spotiipay\Model\SpotiiPay;
use \Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
/**
 * Class MethodAvailabilityObserver
 * @package Spotii\Spotiipay\Observer
 */
class SalesOrderPlaceBefore implements ObserverInterface
{
    const PAYMENT_CODE = 'spotiipay';

    /**
     * @var SpotiiPay
    */
    private $spotiiPayModel;
    private $spotiiApiIdentity;
    private $logger;

    /**
     * @var Data
    */
    private $spotiiHelper;

    /**
     * @var ManagerInterface
    */
    private $messageManager;
    protected $quoteFactory;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;
    /**
     * MethodAvailabilityObserver constructor.
     * @param SpotiiApiConfigInterface $spotiiApiIdentity
     * @param Logger $logger
     */
    public function __construct(
        SpotiiApiConfigInterface $spotiiApiIdentity,
        Logger $logger,
        SpotiiPay $spotiiPayModel,
        Data $spotiiHelper,
        ManagerInterface $messageManager,
        QuoteFactory $quoteFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->spotiiApiIdentity = $spotiiApiIdentity;
        $this->logger = $logger;
        $this->spotiiPayModel = $spotiiPayModel;
        $this->spotiiHelper = $spotiiHelper;
        $this->messageManager = $messageManager;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Hide the method if merchant id, public key & private key are not present
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */


    public function execute(\Magento\Framework\Event\Observer $observer){

        $order = $observer->getEvent()->getOrder();
        $this->spotiiHelper->logSpotiiActions("PayLater observer order ID ".$order->getQuoteId());
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $this->spotiiHelper->logSpotiiActions("PayLater observer payment method ". strval($quote->getPayment()->getMethod()));

        if($quote->getPayment()->getMethod() == "spotiipay"){
            try{
                $order->setCanSendNewEmailFlag(false);
                $order->setEmailSent(false);
            }catch(\Magento\Framework\Exception\LocalizedException $e){
                $this->spotiiHelper->logSpotiiActions('OrderPlaceBefore local'.$e->getMessage());
            }catch(\Exception $e){
                $this->spotiiHelper->logSpotiiActions('OrderPlaceBefore '.$e->getMessage());
            }

        }

    }


}