/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'spotiipay',
                component: 'Spotii_Spotiipay/js/view/payment/method-renderer/spotiipay'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
