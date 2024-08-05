<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Block\SpotiiWidget;

use Magento\Framework\View\Element\Template;
use Spotii\Spotiipay\Model\Config\Container\CartWidgetConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;
use Magento\Store\Model\StoreManagerInterface;	

/**
 * Class Cart
 * @package Spotii\Spotiipay\Block\SpotiiWidget
 */
class Cart extends Template
{
    const MIN_PRICE = 0;
    const MAX_PRICE = 100000;
    const WIDGET_TYPE = "cart";

    /**
     * @var CartWidgetConfigInterface
     */
    private $cartWidgetConfig;
    /**
     * @var SpotiiApiConfigInterface
     */
    private $spotiiApiConfig;

    /**	
     * @var StoreManagerInterface	
     */	
    private $storeManager;

    /**
     * ProductWidget constructor.
     *
     * @param Template\Context $context
     * @param CartWidgetConfigInterface $cartWidgetConfig
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CartWidgetConfigInterface $cartWidgetConfig,
        SpotiiApiConfigInterface $spotiiApiConfig,
        array $data,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,	
        StoreManagerInterface $storeManager
    ) {
        $this->cartWidgetConfig = $cartWidgetConfig;
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get JS Config
     *
     * @return array
     */
    public function getJsConfig()
    {
        $result = [
            'targetXPath' => $this->cartWidgetConfig->getTargetXPath(),
            'renderToPath' => $this->cartWidgetConfig->getRenderToPath(),
            'forcedShow' => $this->cartWidgetConfig->getForcedShow(),
            'alignment' => $this->cartWidgetConfig->getAlignment(),
            'merchantID' => $this->spotiiApiConfig->getMerchantId(),
            'theme' => $this->cartWidgetConfig->getTheme(),
            'widthType' => $this->cartWidgetConfig->getWidthType(),
            'widgetType' => self::WIDGET_TYPE,
            'minPrice' => self::MIN_PRICE,
            'maxPrice' => self::MAX_PRICE,
            'imageUrl' => $this->cartWidgetConfig->getImageUrl(),
            'hideClasses' => $this->cartWidgetConfig->getHideClass(),
            'currency' => $this->storeManager->getStore()->getCurrentCurrencyCode()
        ];

        foreach ($result as $key => $value) {
            if (is_null($result[$key]) || $result[$key] == '') {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
