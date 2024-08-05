<?php

namespace Spotii\Spotiipay\Block\Adminhtml\System\Config;

class SpotiiRegisterAdmin extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * SpotiiRegisterAdmin constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Spotii\Spotiipay\Model\System\Config $config
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Spotii\Spotiipay\Model\System\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Return config settings
     */
    public function getJsonConfig()
    {
        return $this->jsonHelper->jsonEncode($this->config->getSpotiiJsonConfig());
    }
}
