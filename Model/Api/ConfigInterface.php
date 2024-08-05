<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;


/**
 * Interface ConfigInterface
 * @package Spotii\Spotiipay\Model\Api
 */
interface ConfigInterface
{
    /**
     * Get auth token
     * @return mixed
     */
    public function getAuthToken();

    /**
     * Get complete url
     * @param $orderId
     * @param $reference
     * @return mixed
     */
    public function getCompleteUrl($orderId, $reference, $quoteId);

    /**
     * Get cancel url
     * @return mixed
     */
    public function getCancelUrl($orderId, $reference);
}
