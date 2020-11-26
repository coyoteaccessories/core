A custom module for [coyoteaccessories.com](https://coyoteaccessories.com) (Magento 2).  

## How to install
```             
bin/magento maintenance:enable
rm -rf composer.lock
composer clear-cache
composer require --ignore-platform-reqs --prefer-source coyoteaccessories/core:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f
bin/magento maintenance:disable
```

## How to upgrade
```              
bin/magento maintenance:enable
composer remove coyoteaccessories/core
rm -rf composer.lock
composer clear-cache
composer require --ignore-platform-reqs --prefer-source coyoteaccessories/core:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f
bin/magento maintenance:disable 
```