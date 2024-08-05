<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Container;

class CartWidgetIdentity extends Container implements CartWidgetConfigInterface
{
    const XML_PATH_TARGET_XPATH = 'cart/spotiipay/xpath';
    const XML_PATH_PAYMENT_ACTIVE = 'payment/spotiipay/active';
    const XML_PATH_RENDER_TO_PATH = 'cart/spotiipay/render_x_path';
    const XML_PATH_FORCED_SHOW = 'cart/spotiipay/forced_show';
    const XML_PATH_ALIGNMENT = 'cart/spotiipay/alignment';
    const XML_PATH_THEME = 'cart/spotiipay/theme';
    const XML_PATH_WIDTH_TYPE = 'cart/spotiipay/width_type';
    const XML_PATH_IMAGE_URL = 'cart/spotiipay/image_url';
    const XML_PATH_HIDE_CLASS = 'cart/spotiipay/hide_classes';

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PAYMENT_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getTargetXPath()
    {
        $data = $this->getConfigValue(
            self::XML_PATH_TARGET_XPATH,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        return !empty($data) ? explode('|', $data) : '';
    }

    /**
     * @inheritdoc
     */
    public function getRenderToPath()
    {
        $data = $this->getConfigValue(
            self::XML_PATH_RENDER_TO_PATH,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        return !empty($data) ? explode('|', $data) : '';
    }

    /**
     * @inheritdoc
     */
    public function getForcedShow()
    {
        return $this->getConfigValue(
            self::XML_PATH_FORCED_SHOW,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getAlignment()
    {
        return $this->getConfigValue(
            self::XML_PATH_ALIGNMENT,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getTheme()
    {
        return $this->getConfigValue(
            self::XML_PATH_THEME,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getWidthType()
    {
        return $this->getConfigValue(
            self::XML_PATH_WIDTH_TYPE,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getImageUrl()
    {
        return $this->getConfigValue(
            self::XML_PATH_IMAGE_URL,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getHideClass()
    {
        $data = $this->getConfigValue(
            self::XML_PATH_HIDE_CLASS,
            $this->getStore()->getWebsiteId(),
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        return !empty($data) ? explode('|', $data) : '';
    }
}
