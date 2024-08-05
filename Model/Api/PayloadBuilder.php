<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class PayloadBuilder
 * @package Spotii\Spotiipay\Model\Api
 */
class PayloadBuilder
{
    const PRECISION = 4;

    /**
     * @var ConfigInterface
     */
    private $spotiiApiConfig;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * PayloadBuilder constructor.
     * @param ConfigInterface $spotiiApiConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ConfigInterface $spotiiApiConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Build Spotii Checkout Payload
     * @param $quote
     * @param $reference
     * @return array
     */
    public function buildSpotiiCheckoutPayload($quote, $reference)
    {
        $checkoutPayload = $this->buildCheckoutPayload($quote, $reference);
        $orderPayload = $this->buildOrderPayload($quote);
        $customerPayload = $this->buildCustomerPayload($quote);
        $billingPayload = $this->buildBillingPayload($quote);
        $shippingPayload = $this->buildShippingPayload($quote);
        $itemPayload = $this->buildItemPayload($quote);
        $payload = array_merge_recursive(
            $checkoutPayload,
            $orderPayload,
            $customerPayload,
            $billingPayload,
            $shippingPayload,
            $itemPayload
        );
        $payload["completes"] = true;
        return $payload;
    }

    /**
     * Build Checkout Payload from Magento Checkout
     * @param $quote
     * @param $reference
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function buildCheckoutPayload($quote, $reference)
    {
        $orderId = $quote->getReservedOrderId();
        $completeUrl = $this->spotiiApiConfig->getCompleteUrl($orderId, $reference, $quote->getId());
        $cancelUrl = $this->spotiiApiConfig->getCancelUrl($orderId, $reference);
        $checkoutPayload["total"] = strval(round($quote->getGrandTotal(), self::PRECISION));
        $checkoutPayload["currency"] = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $checkoutPayload["description"] = $reference;
        $checkoutPayload["reference"] = $reference;
        $checkoutPayload["display_reference"] = $orderId;
        $checkoutPayload["reject_callback_url"] = $cancelUrl;
        $checkoutPayload["confirm_callback_url"] = $completeUrl;
        return $checkoutPayload;
    }

    /**
     * Build Order Payload
     * @param $quote
     * @return mixed
     */
    private function buildOrderPayload($quote)
    {
        $orderPayload["order"] = [
            "tax_amount" => $quote->getShippingAddress()->getBaseTaxAmount(),
            "shipping_amount" => $quote->getShippingAddress()->getShippingAmount(),
            "discount" => ($quote->getSubtotal() - $quote->getSubtotalWithDiscount())
        ];
        return $orderPayload;
    }

    /**
     * Build Customer Payload
     * @param $quote
     * @return mixed
     */
    private function buildCustomerPayload($quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $customerPayload["order"]["customer"] = [
            "first_name" => $quote->getCustomerFirstname() ? $quote->getCustomerFirstname() : $billingAddress->getFirstname(),
            "last_name" => $quote->getCustomerLastname() ? $quote->getCustomerLastname() : $billingAddress->getLastname(),
            "email" => $quote->getCustomerEmail(),
            "phone" => $billingAddress->getTelephone(),
        ];
        return $customerPayload;
    }

    /**
     * Build Billing Address Payload
     * @param $quote
     * @return mixed
     */
    private function buildBillingPayload($quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $billingPayload["order"]["billing_address"] = [
            "line1" => $billingAddress->getStreetLine(1),
            "line2" => $billingAddress->getStreetLine(2),
            "line4" => $billingAddress->getCity(),
            "state" => $billingAddress->getRegionCode(),
            "postcode" => $billingAddress->getPostcode(),
            "country" => $billingAddress->getCountryId(),
            "phone" => $billingAddress->getTelephone(),
        ];
        return $billingPayload;
    }

    /**
     * Build Shipping Address Payload
     * @param $quote
     * @return mixed
     */
    private function buildShippingPayload($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingPayload["order"]["shipping_address"] = [
            "line1" => $shippingAddress->getStreetLine(1),
            "line2" => $shippingAddress->getStreetLine(2),
            "line4" => $shippingAddress->getCity(),
            "state" => $shippingAddress->getRegionCode(),
            "postcode" => $shippingAddress->getPostcode(),
            "country" => $shippingAddress->getCountryId(),
            "phone" => $shippingAddress->getTelephone(),
        ];
        return $shippingPayload;
    }

    /**
     * Build Cart Item Payload
     * @param $quote
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function buildItemPayload($quote)
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $itemPayload["order"]["lines"] = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $productName = $item->getName();
            $productSku = $item->getSku();
            $productQuantity = $item->getQtyOrdered();
            $itemData = [
                "title" => $productName,
                "sku" => $productSku,
                "quantity" => $productQuantity,
                "price" => strval(round($item->getPriceInclTax(), self::PRECISION)),
                "currency" => $currencyCode,
            ];
            array_push($itemPayload["order"]["lines"], $itemData);
        }
        return $itemPayload;
    }
}
