<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Controller\Standard;

use Spotii\Spotiipay\Controller\AbstractController\SpotiiPay;

/**
 * Class Cancel
 * @package Spotii\Spotiipay\Controller\Standard
 */
class Cancel extends SpotiiPay
{
    /**
     * Cancel the transaction
     */
    public function execute()
    {
        try {
            $this->messageManager->addError("<b>Transaction Cancelled!</b><br> You have cancelled your payment with PayLater.");
            $this->spotiiHelper->logSpotiiActions("Returned from PayLater without completeing payment, order not placed");
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->spotiiHelper->logSpotiiActions("Redirect Exception: " . $e->getMessage());
            $this->messageManager->addError(
                $e->getMessage()
            );
        }
        $this->spotiiHelper->logSpotiiActions("Abandoned Cart");
        $this->_checkoutSession->restoreQuote();
        $this->getResponse()->setRedirect(
        $this->_url->getUrl('checkout/cart')
        );
    }
}
