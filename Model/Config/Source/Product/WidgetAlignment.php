<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Source\Product;

/**
 * Class WidgetAlignment
 * @package Spotii\Spotiipay\Model\Config\Source\Product
 */
class WidgetAlignment implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'center',
                'label' => 'Center',
            ],
            [
                'value' => 'right',
                'label' => 'Right',
            ],
            [
                'value' => 'left',
                'label' => 'Left',
            ],
        ];
    }
}
