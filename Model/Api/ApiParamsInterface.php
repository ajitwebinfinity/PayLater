<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Api;

use Magento\Framework\Http\ZendClient;


/**
 * Interface ApiParamsInterface
 * @package Spotii\Spotiipay\Model\Api
 */
interface ApiParamsInterface
{
    const CONTENT_TYPE_JSON = "application/json";
    const CONTENT_TYPE_XML = "application/xml";
    const TIMEOUT = 80;

}
