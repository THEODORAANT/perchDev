
CREATE TABLE IF NOT EXISTS `__PREFIX__shop_addresses` (
  `addressID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `addressTitle` char(128) DEFAULT 'Default',
  `addressSlug` char(128) DEFAULT 'default',
  `addressFirstName` char(128) NOT NULL DEFAULT '',
  `addressLastName` char(128) NOT NULL DEFAULT '',
  `addressCompany` char(128) NOT NULL DEFAULT '',
  `addressLine1` char(255) NOT NULL DEFAULT '',
  `addressDynamicFields` mediumtext,
  `addressCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `addressUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `addressDeleted` datetime DEFAULT NULL,
  `customerID` int(10) unsigned NOT NULL DEFAULT '0',
  `countryID` int(10) DEFAULT NULL,
  `regionID` int(10) DEFAULT NULL,
  `orderID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`addressID`),
  KEY `idx_customer` (`customerID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_admin_index` (
  `indexID` int(10) NOT NULL AUTO_INCREMENT,
  `itemKey` char(64) NOT NULL DEFAULT '-',
  `itemID` char(32) NOT NULL DEFAULT '0',
  `indexKey` char(64) NOT NULL DEFAULT '-',
  `indexValue` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`indexID`),
  KEY `idx_fk` (`itemKey`,`itemID`),
  KEY `idx_key` (`indexKey`),
  KEY `idx_key_val` (`indexKey`,`indexValue`),
  KEY `idx_keys` (`itemKey`,`indexKey`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_brands` (
  `brandID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brandTitle` varchar(255) NOT NULL DEFAULT '',
  `brandDynamicFields` mediumtext,
  `brandCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `brandUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `brandDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`brandID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_cart` (
  `cartID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `memberID` int(10) unsigned DEFAULT NULL,
  `customerID` int(10) unsigned DEFAULT NULL,
  `currencyID` int(10) unsigned DEFAULT NULL,
  `locationID` int(10) unsigned NOT NULL DEFAULT '0',
  `shippingID` int(10) unsigned DEFAULT NULL,
  `cartPricing` enum('standard','sale','trade') DEFAULT 'standard',
  `cartTotalItems` int(10) unsigned NOT NULL DEFAULT '0',
  `cartTotalProducts` int(10) unsigned NOT NULL DEFAULT '0',
  `cartTotalWithTax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cartTotalWithoutTax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `billingAddress` char(128) DEFAULT NULL,
  `shippingAddress` char(128) DEFAULT NULL,
  `cartProperties` text,
  `cartDiscountCode` char(255) DEFAULT '',
  PRIMARY KEY (`cartID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_cart_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cartID` char(32) NOT NULL DEFAULT '',
  `productID` char(32) DEFAULT NULL,
  `orderID` char(32) DEFAULT NULL,
  `cartData` text,
  PRIMARY KEY (`id`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_cart_items` (
  `itemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cartID` int(10) unsigned NOT NULL  DEFAULT '0',
  `productID` int(10) unsigned NOT NULL  DEFAULT '0',
  `itemQty` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`itemID`),
  KEY `idx_cart` (`cartID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_countries` (
  `countryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL DEFAULT '',
  `iso2` char(2) NOT NULL DEFAULT '',
  `iso3` char(3) NOT NULL DEFAULT '',
  `isonum` int(10) unsigned NOT NULL DEFAULT '0',
  `eu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `countryActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `countryDynamicFields` text,
  PRIMARY KEY (`countryID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_currencies` (
  `currencyID` int(10) NOT NULL AUTO_INCREMENT,
  `currencyCode` char(3) NOT NULL DEFAULT '',
  `currencyNumber` int(10) unsigned DEFAULT NULL,
  `currencyTitle` char(64) NOT NULL DEFAULT '',
  `currencySymbol` char(16) NOT NULL DEFAULT '',
  `currencySymbolPosition` enum('before','after') NOT NULL DEFAULT 'before',
  `currencyDecimals` int(10) unsigned NOT NULL DEFAULT '2',
  `currencyDecimalSeparator` char(16) NOT NULL DEFAULT '.',
  `currencyThousandsSeparator` char(16) NOT NULL DEFAULT ',',
  `currencyRate` decimal(65,4) unsigned NOT NULL DEFAULT '1.0000',
  `currencyActive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `currencyIsCommon` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `currencyDynamicFields` text,
  PRIMARY KEY (`currencyID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_customers` (
  `customerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customerFirstName` char(128) NOT NULL DEFAULT '',
  `customerLastName` char(128) NOT NULL DEFAULT '',
  `customerEmail` char(128) NOT NULL DEFAULT '',
  `customerDynamicFields` mediumtext,
  `customerCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `customerUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `customerDeleted` datetime DEFAULT NULL,
  `memberID` int(10) unsigned NOT NULL DEFAULT '0',
  `customerTaxID` char(255) DEFAULT NULL,
  `customerTaxIDType` char(32) DEFAULT NULL,
  `customerTaxIDStatus` enum('valid','invalid','unchecked') NOT NULL DEFAULT 'unchecked',
  `customerTaxIDLastChecked` datetime DEFAULT NULL,
  `customerTaxIDLastResponse` char(255) DEFAULT NULL,
  PRIMARY KEY (`customerID`),
  KEY `idx_member` (`memberID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_emails` (
  `emailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `emailTitle` varchar(255) NOT NULL  DEFAULT '',
  `emailSlug` varchar(255) NOT NULL  DEFAULT '',
  `emailFor` enum('customer','customer_bcc','admin') NOT NULL DEFAULT 'customer',
  `emailRecipient` varchar(255) DEFAULT NULL,
  `emailStatus` varchar(255) NOT NULL DEFAULT '',
  `emailTemplate` varchar(255) NOT NULL DEFAULT 'order_paid.html',
  `emailActive` varchar(255) NOT NULL  DEFAULT '1',
  `emailDynamicFields` mediumtext,
  `emailCreated` datetime NOT NULL DEFAULT '2016-01-01 00:00:00',
  `emailUpdated` datetime NOT NULL DEFAULT '2016-01-01 00:00:00',
  PRIMARY KEY (`emailID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_index` (
  `indexID` int(10) NOT NULL AUTO_INCREMENT,
  `itemKey` char(64) NOT NULL DEFAULT '-',
  `itemID` char(32) NOT NULL DEFAULT '0',
  `indexKey` char(64) NOT NULL DEFAULT '-',
  `indexValue` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`indexID`),
  KEY `idx_fk` (`itemKey`,`itemID`),
  KEY `idx_key` (`indexKey`),
  KEY `idx_key_val` (`indexKey`,`indexValue`),
  KEY `idx_keys` (`itemKey`,`indexKey`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_option_values` (
  `valueID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `optionID` int(10) unsigned NOT NULL DEFAULT '0',
  `valueTitle` varchar(255) NOT NULL DEFAULT '',
  `valueSKUCode` char(16) DEFAULT NULL,
  `valueOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `valueDynamicFields` mediumtext,
  `valueCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `valueUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `valueDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`valueID`),
  KEY `idx_mod` (`optionID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_options` (
  `optionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `optionTitle` varchar(255) NOT NULL DEFAULT '',
  `optionPrecendence` int(10) unsigned NOT NULL DEFAULT '1',
  `optionDynamicFields` mediumtext,
  `optionCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `optionUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `optionDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`optionID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_order_items` (
  `itemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemType` enum('product','shipping','discount') NOT NULL DEFAULT 'product',
  `orderID` int(10) unsigned DEFAULT NULL,
  `productID` int(10) unsigned DEFAULT NULL,
  `shippingID` int(10) unsigned DEFAULT NULL,
  `itemPrice` decimal(10,2) DEFAULT '0.00',
  `itemTax` decimal(10,2) DEFAULT '0.00',
  `itemDiscount` decimal(10,2) DEFAULT '0.00',
  `itemTaxDiscount` decimal(10,2) DEFAULT '0.00',
  `itemTotal` decimal(10,2) DEFAULT '0.00',
  `itemQty` int(10) unsigned NOT NULL DEFAULT '1',
  `itemTaxRate` char(16) DEFAULT '0.00',
  `itemDynamicFields` mediumtext,
  PRIMARY KEY (`itemID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_orders` (
  `orderID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderStatus` char(128) DEFAULT '',
  `orderInvoiceNumber` char(128) DEFAULT NULL,
  `orderGateway` char(128) NOT NULL DEFAULT '',
  `orderTotal` char(128) NOT NULL DEFAULT '',
  `orderItemsSubtotal` char(128) NOT NULL DEFAULT '0',
  `orderItemsTax` char(128) NOT NULL DEFAULT '0',
  `orderItemsTotal` char(128) NOT NULL DEFAULT '0',
  `orderShippingSubtotal` char(128) NOT NULL DEFAULT '0',
  `orderShippingDiscounts` char(128) NOT NULL DEFAULT '0',
  `orderShippingTax` char(128) NOT NULL DEFAULT '0',
  `orderShippingTaxDiscounts` char(128) NOT NULL DEFAULT '0',
  `orderShippingTotal` char(128) NOT NULL DEFAULT '0',
  `orderDiscountsTotal` char(128) NOT NULL DEFAULT '0',
  `orderTaxDiscountsTotal` char(128) NOT NULL DEFAULT '0',
  `orderSubtotal` char(128) NOT NULL DEFAULT '0',
  `orderTaxTotal` char(128) NOT NULL DEFAULT '0',
  `orderItemsRefunded` char(128) NOT NULL DEFAULT '0',
  `orderTaxRefunded` char(128) NOT NULL DEFAULT '0',
  `orderShippingRefunded` char(128) NOT NULL DEFAULT '0',
  `orderTotalRefunded` char(128) NOT NULL DEFAULT '0',
  `orderTaxID` char(255) DEFAULT NULL,
  `currencyID` int(10) unsigned NOT NULL DEFAULT '0',
  `orderExchangeRate` float(10,5) unsigned DEFAULT NULL,
  `orderShippingWeight` char(128) NOT NULL DEFAULT '0',
  `customerID` int(10) unsigned NOT NULL DEFAULT '0',
  `shippingID` int(10) unsigned DEFAULT '0',
  `orderShippingTaxRate` char(128) NOT NULL DEFAULT '0',
  `orderShippingAddress` int(10) unsigned NOT NULL DEFAULT '0',
  `orderBillingAddress` int(10) unsigned NOT NULL DEFAULT '0',
  `orderGatewayRef` char(255) DEFAULT NULL,
  `orderPricing` enum('standard','sale','trade') NOT NULL DEFAULT 'standard',
  `orderDynamicFields` mediumtext,
  `orderCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `orderUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `orderDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`orderID`),
  KEY `idx_customer` (`customerID`),
  KEY `idx_status` (`orderStatus`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_product_files` (
  `fileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `fileTitle` char(255) NOT NULL DEFAULT '',
  `fileSlug` char(255) NOT NULL DEFAULT '',
  `resourceID` int(10) NOT NULL DEFAULT '0',
  `fileDynamicFields` text,
  `fileOrder` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`fileID`),
  KEY `idx_product` (`productID`),
  KEY `idx_resource` (`resourceID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_product_option_values` (
  `prodoptID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `optionID` int(10) unsigned NOT NULL  DEFAULT '0',
  `valueID` int(10) unsigned NOT NULL DEFAULT '0',
  `valueModPrice` decimal(4,2) NOT NULL DEFAULT '0.00',
  `valueModOperator` enum('+','-','=') NOT NULL DEFAULT '+',
  PRIMARY KEY (`prodoptID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_product_options` (
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `optionID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`productID`,`optionID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_products` (
  `productID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `sku` char(255) NOT NULL DEFAULT '',
  `productSlug` varchar(255) DEFAULT NULL,
  `stock_level` int(10) unsigned DEFAULT NULL,
  `parentID` int(10) unsigned DEFAULT NULL,
  `productVariantDesc` varchar(255) DEFAULT NULL,
  `productOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `productHasVariants` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `productStockOnParent` tinyint(1) unsigned DEFAULT '0',
  `productDynamicFields` mediumtext,
  `productTemplate` char(64) NOT NULL DEFAULT 'product.html',
  `productCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `productUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `productDeleted` datetime DEFAULT NULL,
  `productStatus` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`productID`),
  KEY `idx_sku` (`sku`),
  KEY `idx_del` (`productDeleted`),
  KEY `idx_parent` (`parentID`),
  KEY `idx_status` (`productStatus`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_promotions` (
  `promoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `promoTitle` varchar(255) NOT NULL DEFAULT '',
  `promoDynamicFields` mediumtext,
  `promoFrom` datetime DEFAULT NULL,
  `promoTo` datetime DEFAULT NULL,
  `promoActive` tinyint(1) DEFAULT '1',
  `promoOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `promoCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `promoUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `promoDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`promoID`)
) CHARSET=utf8;

CREATE TABLE `__PREFIX__shop_search` (
  `searchID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemKey` int(10) unsigned NOT NULL DEFAULT '0',
  `itemType` enum('product','brand') DEFAULT 'product',
  `searchBody` text,
  PRIMARY KEY (`searchID`),
  KEY `itemKey` (`itemKey`,`itemType`),
  FULLTEXT KEY `idx_search` (`searchBody`)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_shippings` (
  `shippingID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shippingTitle` varchar(255) NOT NULL DEFAULT '',
  `shippingSlug` varchar(255) NOT NULL DEFAULT '',
  `shippingOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `shippingDynamicFields` mediumtext,
  `shippingCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `shippingUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `shippingDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`shippingID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_tax_exhibits` (
  `exhibitID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderID` int(10) unsigned NOT NULL DEFAULT '0',
  `exhibitType` enum('BILL_ADDRESS','SHIP_ADDRESS','IP_ADDRESS','CARD_ADDRESS','MANUAL') NOT NULL DEFAULT 'IP_ADDRESS',
  `exhibitDetail` char(255) NOT NULL DEFAULT '',
  `exhibitSource` char(255) NOT NULL DEFAULT '',
  `locationID` int(10) unsigned DEFAULT NULL,
  `countryID` int(10) unsigned DEFAULT NULL,
  `exhibitDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exhibitID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_tax_group_rates` (
  `grouprateID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationID` int(10) unsigned NOT NULL DEFAULT '0',
  `groupID` int(10) unsigned NOT NULL DEFAULT '0',
  `rateID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`grouprateID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_tax_groups` (
  `groupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupTitle` char(255) NOT NULL DEFAULT '',
  `groupSlug` char(255) DEFAULT NULL,
  `groupTaxRate` enum('buyer','seller') NOT NULL DEFAULT 'seller',
  `groupDynamicFields` mediumtext,
  `groupCreated` datetime DEFAULT NULL,
  `groupUpdated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `groupDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`groupID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_tax_locations` (
  `locationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationTitle` char(255) NOT NULL DEFAULT '',
  `countryID` int(10) unsigned DEFAULT NULL,
  `regionID` int(10) unsigned DEFAULT NULL,
  `locationIsHome` tinyint(1) unsigned DEFAULT '0',
  `locationIsDefault` tinyint(1) unsigned DEFAULT '0',
  `locationTaxRate` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `locationTaxRateReduced` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `locationDynamicFields` mediumtext,
  `locationCreated` datetime DEFAULT NULL,
  `locationUpdated` datetime DEFAULT NULL,
  `locationDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`locationID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_tax_rates` (
  `rateID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationID` int(10) unsigned NOT NULL DEFAULT '0',
  `rateTitle` char(128) NOT NULL DEFAULT 'Standard',
  `rateValue` decimal(4,2) NOT NULL DEFAULT '0.00',
  `rateDynamicFields` text,
  `rateCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `rateUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rateDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`rateID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_variants` (
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `optionID` int(10) unsigned NOT NULL DEFAULT '0',
  `valueID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`productID`,`optionID`,`valueID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_product_tags` (
  `prodtagID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productID` int(10) unsigned NOT NULL DEFAULT '0',
  `tagID` int(10) unsigned NOT NULL DEFAULT '0',
  `tagExpiry` char(255) DEFAULT NULL,
  `tagDynamicFields` text,
  `tagOrder` int(10) unsigned DEFAULT NULL,
  `tagCreated` datetime DEFAULT NULL,
  `tagModified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tagDeleted` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`prodtagID`),
  KEY `idx_product` (`productID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_orders_meta` (
  `id` char(64) NOT NULL DEFAULT '',
  `metaValue` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_order_statuses` (
  `statusID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statusKey` char(64) NOT NULL DEFAULT '',
  `statusTitle` char(128) NOT NULL DEFAULT '',
  `statusEditable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `statusIndex` int(10) unsigned NOT NULL DEFAULT '101',
  `statusDynamicFields` text,
  `statusCreated` datetime NOT NULL DEFAULT '2016-01-01 00:00:00',
  `statusUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statusDeleted` datetime DEFAULT NULL,
  `statusActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`statusID`),
  KEY `idx_key` (`statusKey`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_order_promotions` (
  `orderpromoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `promoID` int(10) unsigned NOT NULL DEFAULT '0',
  `orderID` int(10) unsigned NOT NULL DEFAULT '0',
  `customerID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderpromoID`),
  KEY `idx_promo` (`promoID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_shipping_zones` (
  `zoneID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zoneTitle` char(255) NOT NULL DEFAULT '',
  `zoneSlug` char(255) DEFAULT NULL,
  `zoneIsDefault` tinyint(1) unsigned DEFAULT '0',
  `zoneActive` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `zoneDynamicFields` mediumtext,
  `zoneCreated` datetime DEFAULT NULL,
  `zoneUpdated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `zoneDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`zoneID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_shipping_zone_countries` (
  `zcID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zoneID` int(10) unsigned NOT NULL DEFAULT '0',
  `countryID` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`zcID`),
  KEY `idx_country` (`countryID`),
  KEY `idx_zone` (`zoneID`)
) CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__shop_sales` (
  `saleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleTitle` varchar(255) NOT NULL DEFAULT '',
  `saleDynamicFields` mediumtext,
  `saleFrom` datetime DEFAULT NULL,
  `saleTo` datetime DEFAULT NULL,
  `saleActive` tinyint(1) DEFAULT '1',
  `saleOrder` int(10) unsigned NOT NULL DEFAULT '1',
  `saleCreated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `saleUpdated` datetime NOT NULL DEFAULT '2015-01-01 00:00:00',
  `saleDeleted` datetime DEFAULT NULL,
  PRIMARY KEY (`saleID`)
) CHARSET=utf8;