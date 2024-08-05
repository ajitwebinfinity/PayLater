<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Container;

use Magento\Store\Model\Store;

/**
 * Interface IdentityInterface
 * @package Spotii\Spotiipay\Model\Config\Container
 */
interface IdentityInterface
{
    /**
     * Check if payment method is enabled
     * @return bool
     */
    public function isEnabled();

    /**
     * Set store
     * @return Store
     */
    public function getStore();

    /**
     * Get Store
     * @param Store $store
     * @return mixed
     */
    public function setStore(Store $store);
}
