ALTER TABLE `__PREFIX__shop_shippings` ADD `shippingOrder` INT(10)  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `shippingSlug`;

ALTER TABLE `__PREFIX__shop_currencies` ADD `currencyDecimalSeparator` CHAR(16) NOT NULL DEFAULT '.' AFTER `currencyDecimals`;

ALTER TABLE `__PREFIX__shop_currencies` ADD `currencyThousandsSeparator` CHAR(16)  NOT NULL  DEFAULT ','  AFTER `currencyDecimalSeparator`;

ALTER TABLE `__PREFIX__shop_products` ADD `productStatus` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '1'  AFTER `productDeleted`;

ALTER TABLE `__PREFIX__shop_products` ADD INDEX `idx_status` (`productStatus`);

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

ALTER TABLE `__PREFIX__shop_countries` ADD `countryDynamicFields` TEXT  NULL  AFTER `countryActive`;
