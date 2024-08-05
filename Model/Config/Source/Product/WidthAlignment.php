<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Source\Product;

/**
 * Class WidthAlignment
 * @package Spotii\Spotiipay\Model\Config\Source\Product
 */
class WidthAlignment implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'thin',
                'label' => 'Thin',
            ],
            [
                'value' => 'thick',
                'label' => 'Thick',
            ],
        ];
    }
}
