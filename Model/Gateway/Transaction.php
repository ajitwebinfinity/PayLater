<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Gateway;

use Spotii\Spotiipay\Helper\Data as SpotiiHelper;
use Spotii\Spotiipay\Model\Api\ConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;

/**
 * Class Transaction
 * @package Spotii\Spotiipay\Model\Gateway
 */
class Transaction
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;
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
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $orderInterface;
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SpotiiHelper
     */
    private $spotiiHelper;

    /**
     * @var SpotiiApiConfigInterface
     */
    protected $spotiiApiIdentity;

    protected $_orderCollectionFactory;

    const PAYMENT_CODE = 'spotiipay';
    /**
     * Transaction constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param SpotiiHelper $spotiiHelper
     * @param ConfigInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Spotii\Spotiipay\Model\Api\ProcessorInterface $spotiiApiProcessor
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        SpotiiHelper $spotiiHelper,
        ConfigInterface $config,
        \Psr\Log\LoggerInterface $logger,
        \Spotii\Spotiipay\Model\Api\ProcessorInterface $spotiiApiProcessor,
        SpotiiApiConfigInterface $spotiiApiConfig,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->spotiiHelper = $spotiiHelper;
        $this->config = $config;
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->spotiiApiProcessor = $spotiiApiProcessor;
        $this->logger = $logger;
        $this->orderInterface = $orderInterface;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Send orders to Spotii
     */
    public function sendOrdersToSpotii()
    {
        $this->spotiiHelper->logSpotiiActions("****Order sync process start****");
        $today = date("Y-m-d H:i:s");
        $this->spotiiHelper->logSpotiiActions("Current date : $today");
        $yesterday = date("Y-m-d H:i:s", strtotime("-1 days"));
        $yesterday = date('Y-m-d H:i:s', strtotime($yesterday));
        $today = date('Y-m-d H:i:s', strtotime($today));

        try {
                $ordersCollection = $this->_orderCollectionFactory->create()
                ->addFieldToFilter(
                'status',
                ['eq' => 'paymentauthorised',
                 'eq' => 'processing']
                )->addFieldToFilter(
                 'created_at',
                ['gteq' => $yesterday]
                )->addFieldToFilter(
                'created_at',
                ['lteq' => $today]
                )->addAttributeToSelect('increment_id');


                $this->spotiiHelper->logSpotiiActions("ordersCollection ".sizeof($ordersCollection));

            $body = $this->_buildOrderPayLoad($ordersCollection);
            $url = $this->spotiiApiConfig->getSpotiiBaseUrl() . '/v1.0/merchant' . '/magento/orders';
            $authToken = $this->config->getAuthToken();
            $this->spotiiApiProcessor->call(
                $url,
                $authToken,
                $body,
                'POST'
            );
            $this->spotiiHelper->logSpotiiActions("****Order sync process end****");
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions("Error while sending order to PayLater" . $e->getMessage());
        }
    }

    /**
     * Build Payload
     *
     * @param null $ordersCollection
     * @return array
     */
    private function _buildOrderPayLoad($ordersCollection = null)
    {
        $body = [];
        if ($ordersCollection) {
            foreach ($ordersCollection as $orderObj) {
                $orderIncrementId = $orderObj->getIncrementId();
                $order = $this->orderInterface->loadByIncrementId($orderIncrementId);
                $payment = $order->getPayment();
                $paymentMethod =$payment->getMethod();
                $this->spotiiHelper->logSpotiiActions("Orders ".$orderIncrementId);
                if($paymentMethod == self::PAYMENT_CODE){
                $billing = $order->getBillingAddress();
                $orderForSpotii = [
                    'order_number' => $orderIncrementId,
                    'payment_method' => $paymentMethod,
                    'amount' => strval(round($order->getGrandTotal(), \Spotii\Spotiipay\Model\Api\PayloadBuilder::PRECISION)),
                    'currency' => $order->getOrderCurrencyCode(),
                    'reference' => $payment->getLastTransId(),
                    'customer_email' => $billing->getEmail(),
                    'customer_phone' => $billing->getTelephone(),
                    'billing_address1' => $billing->getStreetLine(1),
                    'billing_address2' => $billing->getStreetLine(2),
                    'billing_city' => $billing->getCity(),
                    'billing_state' => $billing->getRegionCode(),
                    'billing_postcode' => $billing->getPostcode(),
                    'billing_country' => $billing->getCountryId(),
                    'merchant_id' => $this->spotiiApiConfig->getMerchantId()
                ];
                array_push($body, $orderForSpotii);
            }
            }
        }
        return $body;
    }
}
