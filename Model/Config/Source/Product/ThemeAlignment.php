<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Source\Product;

/**
 * Class ThemeAlignment
 * @package Spotii\Spotiipay\Model\Config\Source\Product
 */
class ThemeAlignment implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'light',
                'label' => 'Light',
            ],
            [
                'value' => 'dark',
                'label' => 'Dark',
            ],
        ];
    }
}
