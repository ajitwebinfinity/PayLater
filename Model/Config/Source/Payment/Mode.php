<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Source\Payment;

/**
 * Class Mode
 * @package Spotii\Spotiipay\Model\Config\Source\Payment
 */
class Mode implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'live',
                'label' => 'Live',
            ],
            [
                'value' => 'sandbox',
                'label' => 'Sandbox',
            ]
        ];
    }
}
