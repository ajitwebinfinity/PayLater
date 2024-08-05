<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Block\SpotiiWidget;

use Magento\Framework\View\Element\Template;
use Spotii\Spotiipay\Model\Config\Container\CatalogWidgetConfigInterface;
use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;
use Magento\Store\Model\StoreManagerInterface;	
/**
 * Class ProductView
 * @package Spotii\Spotiipay\Block\SpotiiWidget
 */
class Catalogue extends Template
{
    const MIN_PRICE = 0;
    const MAX_PRICE = 100000;
    const WIDGET_TYPE = "catalogue_page";

    /**
     * @var CatalogWidgetConfigInterface
     */
    private $catalogWidgetConfig;
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
     * @param CatalogWidgetConfigInterface $catalogWidgetConfig
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CatalogWidgetConfigInterface $catalogWidgetConfig,
        SpotiiApiConfigInterface $spotiiApiConfig,
        \Magento\Framework\Registry $registry,	 
        array $data,	
        StoreManagerInterface $storeManager  
        ) {
        $this->catalogWidgetConfig = $catalogWidgetConfig;
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
            'targetXPath' => $this->catalogWidgetConfig->getTargetXPath(),
            'renderToPath' => $this->catalogWidgetConfig->getRenderToPath(),
            'forcedShow' => $this->catalogWidgetConfig->getForcedShow(),
            'alignment' => $this->catalogWidgetConfig->getAlignment(),
            'merchantID' => $this->spotiiApiConfig->getMerchantId(),
            'theme' => $this->catalogWidgetConfig->getTheme(),
            'widthType' => $this->catalogWidgetConfig->getWidthType(),
            'widgetType' => self::WIDGET_TYPE,
            'minPrice' => self::MIN_PRICE,
            'maxPrice' => self::MAX_PRICE,
            'imageUrl' => $this->catalogWidgetConfig->getImageUrl(),
            'hideClasses' => $this->catalogWidgetConfig->getHideClass(),
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
