<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Block\SpotiiWidget;

use Magento\Framework\View\Element\Template;
use Spotii\Spotiipay\Model\Config\Container\ProductWidgetConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;
use Magento\Store\Model\StoreManagerInterface;	
/**
 * Class ProductView
 * @package Spotii\Spotiipay\Block\SpotiiWidget
 */
class ProductView extends Template
{
    const MIN_PRICE = 0;
    const MAX_PRICE = 100000;
    const WIDGET_TYPE = "product_page";

    /**
     * @var ProductWidgetConfigInterface
     */
    private $productWidgetConfig;
    /**
     * @var SpotiiApiConfigInterface
     */
    private $spotiiApiConfig;

    /**	
     * @var StoreManagerInterface	
     */	
    private $storeManager;	
    /**	
     * @var registry	
     */	
    private $registry;
    /**
     * ProductWidget constructor.
     *
     * @param Template\Context $context
     * @param ProductWidgetConfigInterface $productWidgetConfig
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProductWidgetConfigInterface $productWidgetConfig,
        SpotiiApiConfigInterface $spotiiApiConfig,
        \Magento\Framework\Registry $registry,	 
        array $data,	
        StoreManagerInterface $storeManager  
        ) {
        $this->productWidgetConfig = $productWidgetConfig;
        $this->spotiiApiConfig = $spotiiApiConfig;
        $this->storeManager = $storeManager;	
        $this->registry = $registry;
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
            'targetXPath' => $this->productWidgetConfig->getTargetXPath(),
            'renderToPath' => $this->productWidgetConfig->getRenderToPath(),
            'forcedShow' => $this->productWidgetConfig->getForcedShow(),
            'alignment' => $this->productWidgetConfig->getAlignment(),
            'merchantID' => $this->spotiiApiConfig->getMerchantId(),
            'theme' => $this->productWidgetConfig->getTheme(),
            'widthType' => $this->productWidgetConfig->getWidthType(),
            'widgetType' => self::WIDGET_TYPE,
            'minPrice' => self::MIN_PRICE,
            'maxPrice' => self::MAX_PRICE,
            'imageUrl' => $this->productWidgetConfig->getImageUrl(),
            'hideClasses' => $this->productWidgetConfig->getHideClass(),
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
