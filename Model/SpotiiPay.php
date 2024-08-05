<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Sales\Model\Order;

/**
 * Class SpotiiPay
 * @package Spotii\Spotiipay\Model
 */
class SpotiiPay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_CODE = 'spotiipay';
    const ADDITIONAL_INFORMATION_KEY_ORDERID = 'spotii_order_id';
    const SPOTII_CAPTURE_EXPIRY = 'spotii_capture_expiry';

    /**
     * @var string
     */
    protected $_code = self::PAYMENT_CODE;
    /**
     * @var bool
     */
    protected $_isGateway = true;

    protected $_isOffline = false;

    /**
     * @var bool
     */
    protected $_isInitializeNeeded = false;
    /**
     * @var bool
     */
    protected $_canOrder = true;
    /**
     * @var bool
     */
    protected $_canAuthorize = true;
    /**
     * @var bool
     */
    protected $_canCapture = true;
    /**
     * @var bool
     */
    protected $_canRefund = true;
    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;
    /**
     * @var bool
     */
    protected $_canUseInternal = false;
    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

    /**
     * @var Api\PayloadBuilder
     */
    private $apiPayloadBuilder;
    /**
     * @var Api\ConfigInterface
     */
    private $spotiiApiConfig;
    /**
     * @var Config\Container\SpotiiApiIdentity
     */
    private $spotiiApiIdentity;
    /**
     * @var Api\ProcessorInterface
     */
    private $spotiiApiProcessor;
    /**
     * @var Order\Payment\Transaction\BuilderInterface
     */
    private $_transactionBuilder;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Spotii\Spotiipay\Helper\Data
     */
    protected $spotiiHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * SpotiiPay constructor.
     * @param Context $context
     * @param Config\Container\SpotiiApiIdentity $spotiiApiIdentity
     * @param Api\ConfigInterface $spotiiApiConfig
     * @param \Spotii\Spotiipay\Helper\Data $spotiiHelper
     * @param Api\PayloadBuilder $apiPayloadBuilder
     * @param Api\ProcessorInterface $spotiiApiProcessor
     * @param Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $mageLogger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        Config\Container\SpotiiApiIdentity $spotiiApiIdentity,
        Api\ConfigInterface $spotiiApiConfig,
        \Spotii\Spotiipay\Helper\Data $spotiiHelper,
        Api\PayloadBuilder $apiPayloadBuilder,
        Api\ProcessorInterface $spotiiApiProcessor,
        Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $mageLogger,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->apiPayloadBuilder = $apiPayloadBuilder;
        $this->spotiiHelper = $spotiiHelper;
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->spotiiApiIdentity = $spotiiApiIdentity;
        $this->spotiiApiProcessor = $spotiiApiProcessor;
        $this->_transactionBuilder = $transactionBuilder;
        $this->jsonHelper = $jsonHelper;
        $this->messageManager = $messageManager;
        $this->dateTime = $dateTime;
        $this->checkoutSession = $checkoutSession;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $mageLogger
        );
    }

    /**
     * Get Spotii checkout url
     * @param $quote
     * @return bool
     * @throws LocalizedException
     */
    public function getSpotiiCheckoutUrl($quote)
    {
        $reference = uniqid() . "-" . $quote->getReservedOrderId();
        $this->spotiiHelper->logSpotiiActions("Reference Id : $reference");
        $payment = $quote->getPayment();
        $payment->setAdditionalInformation(self::ADDITIONAL_INFORMATION_KEY_ORDERID, $reference);
        $payment->save();
        $response = $this->getSpotiiRedirectUrl($quote, $reference);
        $this->spotiiHelper->logSpotiiActions("response : $response");
        $result = $this->jsonHelper->jsonDecode($response, true);
        $orderUrl = array_key_exists('checkout_url', $result) ? $result['checkout_url'] : false;
        $this->spotiiHelper->logSpotiiActions("Order url : $orderUrl");
        if (!$orderUrl) {
            $this->spotiiHelper->logSpotiiActions("No Token response from API");
            throw new LocalizedException(__('There is an issue processing your order.'));
        }
        return $orderUrl;
    }

    /**
     * Get Spotii redirect url
     * @param $quote
     * @param $reference
     * @return mixed
     * @throws LocalizedException
     */
    public function getSpotiiRedirectUrl($quote, $reference)
    {
        $url = $this->spotiiApiIdentity->getSpotiiBaseUrl() . '/api/v1.0/checkouts/';
        $requestBody = $this->apiPayloadBuilder->buildSpotiiCheckoutPayload($quote, $reference);
        try {
            $authToken = $this->spotiiApiConfig->getAuthToken();
            $response = $this->spotiiApiProcessor->call(
                $url,
                $authToken,
                $requestBody,
                'POST'
            );
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
        return $response;
    }


    /**
     * Check if order total is matching
     *
     * @param float $magentoAmount
     * @param float $spotiiAmount
     * @return bool
     */
    public function isOrderAmountMatched($magentoAmount, $spotiiAmount, $magentoCurrency, $spotiiCurrency)
    {
        $precision = \Spotii\Spotiipay\Model\Api\PayloadBuilder::PRECISION;

            if ($spotiiCurrency != $magentoCurrency){
                 if($spotiiCurrency == "AED"){
                 switch($magentoCurrency){
                     case "USD":
                        $magentoAmount=(round($magentoAmount, $precision))*3.6730 ;
                     break;
                     case "SAR":
                         $magentoAmount=(round($magentoAmount, $precision))*0.9604;
                     break;
                     case "BHD":
                        $magentoAmount=(round($magentoAmount, $precision))*9.7400;
                     break;
                     case "OMR":
                        $magentoAmount=(round($magentoAmount, $precision))*9.5500;
                     break;
                     case "KWD":
                        $magentoAmount=(round($magentoAmount, $precision))*12.1300;
                     break;
                 }
              }
                 if(abs( round($spotiiAmount, $precision) - round($magentoAmount, $precision) <6)){
                     return true;
                 }

             }else if (round($spotiiAmount, $precision) == round($magentoAmount, $precision)){
                     return true;
             }else {
                     return false;
             }
    }

    /**
     * Send authorize request to gateway
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->spotiiHelper->logSpotiiActions("****Authorization start****");
        $reference = $payment->getAdditionalInformation(self::ADDITIONAL_INFORMATION_KEY_ORDERID);
        $currency =$payment->getOrder()->getGlobalCurrencyCode();
        $grandTotalInCents = round($amount, \Spotii\Spotiipay\Model\Api\PayloadBuilder::PRECISION);
        $this->spotiiHelper->logSpotiiActions("PayLater Reference ID : $reference");
        $this->spotiiHelper->logSpotiiActions("Magento Order Total : $grandTotalInCents");
        $result = $this->getSpotiiOrderInfo($reference);
        $spotiiOrderTotal = isset($result['total']) ?
                                $result['total'] :
                                null;
        $spotiiOrderCurr = isset($result['currency']) ?
        $result['currency'] :
        null;
        $this->spotiiHelper->logSpotiiActions("PayLater Order Total : $spotiiOrderTotal");
        $this->spotiiHelper->logSpotiiActions("PayLater Order Currency : $spotiiOrderCurr");
        if ($spotiiOrderTotal != null
        && !$this->isOrderAmountMatched($grandTotalInCents, $spotiiOrderTotal, $currency, $spotiiOrderCurr)) {
            $this->spotiiHelper->logSpotiiActions("PayLater gateway has rejected request due to invalid order total");
            throw new LocalizedException(__('PayLater gateway has rejected request due to invalid order total.'));
        } else {
            $payment->setAdditionalInformation('payment_type', $this->getConfigData('payment_action'));
            $this->spotiiHelper->logSpotiiActions("Authorization successful");
            $this->spotiiHelper->logSpotiiActions("Authorization end");
        }
    }

    /**
     * Capture at Magento
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $reference = $payment->getAdditionalInformation(self::ADDITIONAL_INFORMATION_KEY_ORDERID);
        $payment->setAdditionalInformation('payment_type', $this->getConfigData('payment_action'));
    }

    public function capturePostSpotii(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->spotiiHelper->logSpotiiActions("****Capture at Magento start****");
        if ($amount <= 0) {
            throw new LocalizedException(__('Invalid amount for capture.'));
        }
        $reference = $payment->getAdditionalInformation(self::ADDITIONAL_INFORMATION_KEY_ORDERID);
        $currency =$payment->getOrder()->getGlobalCurrencyCode();
        $grandTotalInCents = round($amount, \Spotii\Spotiipay\Model\Api\PayloadBuilder::PRECISION);
        $this->spotiiHelper->logSpotiiActions("PayLater Reference ID : $reference");
        $this->spotiiHelper->logSpotiiActions("Magento Order Total : $grandTotalInCents");

        $result = $this->getSpotiiOrderInfo($reference);
        $spotiiOrderTotal = isset($result['total']) ?
                                $result['total'] :
                                null;
        $spotiiOrderCurr = isset($result['currency']) ?
        $result['currency'] :
        null;
        $this->spotiiHelper->logSpotiiActions("PayLater Order Total : $spotiiOrderTotal");

        if ($spotiiOrderTotal != null
            && !$this->isOrderAmountMatched($grandTotalInCents, $spotiiOrderTotal, $currency, $spotiiOrderCurr)) {
            $this->spotiiHelper->logSpotiiActions("PayLater gateway has rejected request due to invalid order total");
            throw new LocalizedException(__('PayLater gateway has rejected request due to invalid order total.'));
        }

        $captureExpiration = (isset($result['capture_expiration']) && $result['capture_expiration']) ? $result['capture_expiration'] : null;
        if ($captureExpiration === null) {
            $this->spotiiHelper->logSpotiiActions("Not authorized on PayLater");
            throw new LocalizedException(__('Not authorized on PayLater. Please try again.'));
        }
        $captureExpirationTimestamp = $this->dateTime->timestamp($captureExpiration);
        $currentTimestamp = $this->dateTime->timestamp("now");
        $this->spotiiHelper->logSpotiiActions("Capture Expiration Timestamp : $captureExpirationTimestamp");
        $this->spotiiHelper->logSpotiiActions("Current Timestamp : $currentTimestamp");
        if ($captureExpirationTimestamp >= $currentTimestamp) {
            $payment->setAdditionalInformation('payment_type', $this->getConfigData('payment_action'));
            $this->spotiiCapture($reference);
            $payment->setTransactionId($reference)->setIsTransactionClosed(false);
            $this->spotiiHelper->logSpotiiActions("Authorized on PayLater");
            $this->spotiiHelper->logSpotiiActions("****Capture at Magento end****");
        } else {
            $this->spotiiHelper->logSpotiiActions("Unable to capture amount. Time expired.");
            throw new LocalizedException(__('Unable to capture amount.'));
        }
    }

    /**
     * Set Spotii Capture Expiry
     *
     * @param string $reference
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return void
     * @throws LocalizedException
     */
    public function setSpotiiCaptureExpiry($reference, $payment)
    {
        $spotiiOrder = $this->getSpotiiOrderInfo($reference);
        if (isset($spotiiOrder['capture_expiration']) && $spotiiOrder['capture_expiration']) {
            $payment->setAdditionalInformation(self::SPOTII_CAPTURE_EXPIRY, $spotiiOrder['capture_expiration']);
            $payment->save();
        }
    }

    /**
     * Get order info from Spotii
     *
     * @param string $reference
     * @throws LocalizedException
     * @return array
     */
    public function getSpotiiOrderInfo($reference)
    {
        $this->spotiiHelper->logSpotiiActions("****Getting order from PayLater****");
        $url = $this->spotiiApiIdentity->getSpotiiBaseUrl() . '/api/v1.0/orders' . '/' . $reference . '/';
        $authToken = $this->spotiiApiConfig->getAuthToken();
        $result = $this->spotiiApiProcessor->call(
            $url,
            $authToken,
            null,
            'GET'
        );
        $result = $this->jsonHelper->jsonDecode($result, true);
        if (isset($result['status']) && $result['status'] == \Spotii\Spotiipay\Model\Api\ProcessorInterface::BAD_REQUEST) {
            throw new LocalizedException(__('Invalid checkout. Please retry again.'));
            return $this;
        }
        $this->spotiiHelper->logSpotiiActions("****Order successfully fetched from PayLater****");
        return $result;
    }

    /**
     * Capture payment at Spotii
     *
     * @param $reference
     * @return mixed
     * @throws LocalizedException
     */
    public function spotiiCapture($reference)
    {
        try {
            $this->spotiiHelper->logSpotiiActions("****Capture at PayLater Start****");
            $url = $this->spotiiApiIdentity->getSpotiiBaseUrl() . '/api/v1.0/orders' . '/' . $reference . '/capture' . '/';
            $authToken = $this->spotiiApiConfig->getAuthToken();
            $response = $this->spotiiApiProcessor->call(
                $url,
                $authToken,
                null,
                'POST'
            );
            $this->spotiiHelper->logSpotiiActions("****Capture at PayLater End****");
        } catch (\Exception $e) {
            $this->spotiiHelper->logSpotiiActions($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
        return $response;
    }

    /**
     * Create refund
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param $amount
     * @return $this
     * @throws LocalizedException
     */


    public function processBeforeRefund($invoice, $payment){}
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->spotiiHelper->logSpotiiActions("****Refund Start****");
        $orderId = $payment->getAdditionalInformation(self::ADDITIONAL_INFORMATION_KEY_ORDERID);
        $this->spotiiHelper->logSpotiiActions("Order Id : $orderId");
        if ($orderId) {
            $currency = $payment->getOrder()->getGlobalCurrencyCode();
            $this->spotiiHelper->logSpotiiActions("Currency : $currency");
            try {
                $url = $this->spotiiApiIdentity->getSpotiiBaseUrl() . '/api/v1.0/orders' . '/' . $orderId . '/refund' . '/';
                $authToken = $this->spotiiApiConfig->getAuthToken();
                $requestPayload = [
                    "total" => round($amount, \Spotii\Spotiipay\Model\Api\PayloadBuilder::PRECISION),
                    "currency" => $currency
                ];
                $this->spotiiApiProcessor->call(
                    $url,
                    $authToken,
                    $requestPayload,
                    'POST'
                );
                $this->spotiiHelper->logSpotiiActions("****Refund End****");
                return $this;
            } catch (\Exception $e) {
                $this->spotiiHelper->logSpotiiActions($e->getMessage());
                throw new LocalizedException(__($e->getMessage()));
            }
        } else {
            $message = __('There are no PayLater payment linked to this order. Please use refund offline for this order.');
            throw new LocalizedException($message);
        }
    }
    public function processCreditmemo($creditmemo, $payment){}

    /**
     * Create transaction
     * @param $order
     * @param $reference
     * @return mixed
     */
    public function createTransaction($order, $reference, $type)
    {
        $this->spotiiHelper->logSpotiiActions("****Transaction start****");
        $this->spotiiHelper->logSpotiiActions("Order Id : " . $order->getId());
        $this->spotiiHelper->logSpotiiActions("Reference Id : $reference");
        $payment = $order->getPayment();
        $payment->setLastTransId($reference);
        $payment->setTransactionId($reference);
        $formattedPrice = $order->getBaseCurrency()->formatTxt(
            $order->getGrandTotal()
        );

        if ($type == \Magento\Sales\Model\Order\Payment\Transaction::TYPE_ORDER) {
            $message = __('Order placed for amount %1.', $formattedPrice);
            $transactionId = $reference;
        } else {
            $message = __('Payment processed for amount %1.', $formattedPrice);
            $transactionId = $reference . '-' . $type;
        }
        $this->spotiiHelper->logSpotiiActions($message);
        $transaction = $this->_transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($transactionId)
            ->setFailSafe(true)
            ->build($type);

        $payment->addTransactionCommentsToOrder(
            $transaction,
            $message
       );

        $payment->setParentTransactionId(null);
        $payment->save();
        // $quote->collectTotals()->save();
        $order->save();
        $transactionId = $transaction->save()->getTransactionId();
        $this->spotiiHelper->logSpotiiActions("Transaction Id : $transactionId");
        $this->spotiiHelper->logSpotiiActions("****Transaction End****");
        return $transactionId;
    }

    public function canRefund() {
        return true;
    }

    public function isOffline() {
        return false;
    }
}
