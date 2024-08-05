## Magento 2 extension for Spotii Payment Gateway

Use this extention for seamless integration with Spotii as payment gateway and your Magento 2 store.

### Installation Using Composer (Recommended)
1. `$ composer require spotii/spotiipay`
2. `php bin/magento setup:upgrade`
3. `php bin/magento setup:di:compile`
4. `php bin/magento setup:static-content:deploy`
5. `php bin/magento cache:clean`

### Manual Setup
1. Sign up for Spotii account at `https://dashboard.dev.spotii.me/merchant/signup/`
2. In your Magento 2 `[ROOT]/app/code/` create folder called `Spotii/Spotiipay`.
3. Download and extract files from this repo to the folder.
4. Open the command line.
5. Enable Spotii by running command below:
`php bin/magento module:enable Spotii_Spotiipay`
6. Magento setup upgrade:
`php bin/magento setup:upgrade`
7. Magento Dependencies Injection Compile:
`php bin/magento setup:di:compile`
8. Magento Static Content deployment:
`php bin/magento setup:static-content:deploy`
9. Login to Magento Admin and navigate to System/Cache Management
10. Flush the cache storage by selecting Flush Cache Storage

For more information on installation and configuration, please visit our [docs](https://docs.spotii.me/#magento-2). 
