<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;

use Magento\Framework\Http\ZendClient;

/**
 * Interface ProcessorInterface
 * @package Spotii\Spotiipay\Model\Api
 */
interface ProcessorInterface
{
    const BAD_REQUEST = 400;

    /**
     * Call to Spotii Gateway
     *
     * @param $url
     * @param $authToken
     * @param bool $body
     * @param $method
     * @return mixed
     */
    public function call(
        $url,
        $authToken = null,
        $body = false,
        $method = ZendClient::GET
    );
}
