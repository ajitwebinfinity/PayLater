<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Controller\Standard;

use Spotii\Spotiipay\Controller\AbstractController\SpotiiPay;

/**
 * Class Complete
 * @package Spotii\Spotiipay\Controller\Standard
 */
class Complete extends SpotiiPay
{
    /**
     * Complete the transaction
     */
    public function execute()
    {
        $redirect = 'checkout/onepage/success';
        // Create order before redirect to success
        $quote = $this->_checkoutSession->getQuote();
        $quoteId = $quote->getId();
        $quote->collectTotals()->save();
        $order = $this->_quoteManagement->submit($quote);

        $payment = $quote->getPayment();
        $payment->setMethod('spotiipay');
        $payment->save();
        $quote->reserveOrderId();
        $quote->setPayment($payment);
        $quote->save();
        $this->_checkoutSession->replaceQuote($quote);
        $reference = $payment->getAdditionalInformation('spotii_order_id');
        $this->_spotiipayModel->createTransaction(
            $order,
            $reference,
            \Magento\Sales\Model\Order\Payment\Transaction::TYPE_ORDER
        );
        $order->save(); // **
        $this->_checkoutSession->setLastQuoteId($quoteId);

        try {
            $this->spotiiHelper->logSpotiiActions("Returned from PayLater.");

            $orderId = $this->getRequest()->getParam("id");
            $reference = $this->getRequest()->getParam("magento_spotii_id");
            $quoteId = $this->getRequest()->getParam("quote_id");

            $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
            $this->_spotiipayModel->capturePostSpotii($order->getPayment(), $order->getGrandTotal());
            if (!$order->hasInvoices()) {
                $invoice = $order->prepareInvoice();
                $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                $invoice->register();
                $order->addRelatedObject($invoice);
            }
            $order->setState('processing')->save();
            $order->setStatus('processing')->save();
            $order->save();

            if ($order) {

                $this->_spotiipayModel->createTransaction(
                    $order,
                    $reference,
                    \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE
                );
                // $quote->collectTotals()->save();
                $this->spotiiHelper->logSpotiiActions("Created transaction with reference $reference");

                // send email
               try {
                    $this->_orderSender->send($order);
                } catch (\Exception $e) {
                   $this->_helper->debug("Transaction Email Sending Error: " . json_encode($e));
                };
                $this->_checkoutSession->setLastSuccessQuoteId($quoteId);
                $this->_checkoutSession->setLastQuoteId($quoteId);
                $this->_checkoutSession->setLastOrderId($order->getEntityId());
                $this->_checkoutSession->setLastRealOrderId($orderId);
                $this->messageManager->addSuccess("<b>Success! Payment completed!</b><br>Thank you for your payment, your order with PayLater has been placed.");
                $this->getResponse()->setRedirect(
                    $this->_url->getUrl('checkout/onepage/success')
               );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->spotiiHelper->logSpotiiActions("Transaction Exception: " . $e->getMessage());
            $this->messageManager->addError(
                $e->getMessage()
            );
        }
        $this->getResponse()->setRedirect(
            $this->_url->getUrl($redirect)
       );
    }
}
