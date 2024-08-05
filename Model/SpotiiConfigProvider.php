<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class SpotiiConfigProvider
 * @package Spotii\Spotiipay\Model
 */
class SpotiiConfigProvider implements ConfigProviderInterface
{

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'spotiipay' => [
                    'methodCode' => "spotiipay"
                ]
            ]
        ];
    }
}
