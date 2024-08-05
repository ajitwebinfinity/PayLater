define(
    [
        'jquery',
        'uiComponent',
        'mage/translate',
        'jquery/ui',
        'jquery/validate'
    ],
    function ($, Class, $t) {
        'use strict';

        return Class.extend({

                defaults: {
                    $spotiiMerchantId: null,
                    selector: 'spotiipay_spotiipay',
                    $container: null,
                    $form: null,
                },

                /**
                 * Set list of observable attributes
                 * @returns {exports.initObservable}
                 */
                initObservable: function () {
                    var self = this;

                    self.$spotiiConfig = $('#spotii_config');
                    self.$spotiiPaymentHeader = $('#payment_' + self.getCountry() + '_' + self.selector
                        + '_payment-head');
                    self.$spotiiMerchantId = $('#payment_' + self.getCountry() + '_' + self.selector
                        + '_payment_merchant_id').val();
                    self.$container = $('#spotii_config');

                    if (self.$spotiiMerchantId) {
                        self.hideSpotiiConfig();
                    }
                    else {
                        self.showSpotiiConfig();
                    }

                    if (!self.$form) {
                        self.generateSimplePathForm();
                    }

                    self._super();

                    self.initEventHandlers();

                    return self;
                },

                /**
                 * Init event handlers
                 */
                initEventHandlers: function () {
                    var self = this;

                    $('#spotii-config-skip').click(function () {
                        self.hideSpotiiConfig();
                        return false;
                    });
                },

                /**
                 * Sets up dynamic form for capturing popup/form input for simple path setup.
                 */
                generateSimplePathForm: function () {

                    this.$form = new Element('form', {
                        method: 'post',
                        action: this.spotiiUrl,
                        id: 'spotii_config_form',
                        target: 'config',
                        novalidate: 'novalidate',
                    });

                    this.$container.wrap(this.$form);
                },

                /**
                 * display spotii simple path config section
                 */
                showSpotiiConfig: function () {
                    this.$spotiiConfig.show();
                    if (this.$spotiiPaymentHeader.hasClass('open')) {
                        this.$spotiiPaymentHeader.click();
                    }
                },

                /**
                 * hide spotii simple path config.
                 */
                hideSpotiiConfig: function () {
                    this.$spotiiConfig.hide();
                    if (!this.$spotiiPaymentHeader.hasClass('open')) {
                        this.$spotiiPaymentHeader.click();
                    }
                },

                /**
                 * Get country code
                 * @returns {String}
                 */
                getCountry: function () {
                    return this.co.toLowerCase();
                },
            }
        );
    }
);
