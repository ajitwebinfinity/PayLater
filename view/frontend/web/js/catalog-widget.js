/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */
define(['jquery', 'ko', 'uiComponent', 'domReady!'], function (
	$,
	ko,
	Component
) {
	'use strict';

	return Component.extend({
		initialize: function () {
			//initialize parent Component
			this._super();
			this.loadSpotiiScript();
		},

		spotiiCatalogWidget: function(){
			var self = this;
			console.log(self.jsConfig, 'self');
			let renderToPathClass = self.jsConfig.renderToPath;
			const allProducts = document.getElementsByClassName(renderToPathClass);
			for(let product of allProducts){
				if(product.dataset.priceType === "finalPrice"){
					let targetXPath = `#${product.id}`;
					let renderToPath;
					if(product.parentElement.parentElement.className === "special-price"){
						const spotiiReference = `spotii-${(Math.floor(1000 + Math.random() * 9000))}`;
						product.parentElement.parentElement.parentElement.classList.add(spotiiReference);
						renderToPath = `.${spotiiReference}`;
					}
					else{
						renderToPath = `#${product.id}`;
					}
				window.loadSpotiiWidget(window, document, targetXPath, renderToPath, self.jsConfig.currency);
				}
			}

		},

		loadSpotiiScript: function(){
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.onload = ()=> {
			this.spotiiCatalogWidget();
			};
			script.src = 'https://widget.spotii.me/v1/javascript/spotii-catalog-widget.js';
			document.body.appendChild(script);
			console.log("dom loaded");
		},

	});
});
