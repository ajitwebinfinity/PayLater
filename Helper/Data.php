<?php

namespace Spotii\Spotiipay\Helper;

use Spotii\Spotiipay\Model\Config\Container\SpotiiApiConfigInterface;

/**
 * Spotii Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SPOTII_LOG_FILE_PATH = '/var/log/spotiipay.log';

    /**
     * @var SpotiiApiConfigInterface
     */
    private $spotiiApiConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param SpotiiApiConfigInterface $spotiiApiConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        SpotiiApiConfigInterface $spotiiApiConfig
    ) {
        $this->spotiiApiConfig = $spotiiApiConfig;
        parent::__construct($context);
    }

    /**
     * Dump Spotii log actions
     *
     * @param string $msg
     * @return void
     */
    public function logSpotiiActions($data = null)
    {
        if ($this->spotiiApiConfig->isLogTrackerEnabled()) {
            $writer = new \Zend_Log_Writer_Stream(BP . self::SPOTII_LOG_FILE_PATH);
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($data);
        }
    }
}
