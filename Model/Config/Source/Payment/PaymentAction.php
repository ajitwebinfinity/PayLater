<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

declare(strict_types=1);

namespace Spotii\Spotiipay\Model\Config\Source\Payment;

use Magento\Framework\Option\ArrayInterface;

/**
 * Spotii Payment Action Dropdown source
 */
class PaymentAction implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Spotii\Spotiipay\Model\SpotiiPay::ACTION_AUTHORIZE,
                'label' => __('Authorize Only'),
            ],
            [
                'value' => \Spotii\Spotiipay\Model\SpotiiPay::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ]
        ];
    }
}
