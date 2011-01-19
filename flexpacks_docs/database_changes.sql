Toolbox:
/* also update offer creation script (publish_live_offers) */

ALTER TABLE `package` ADD `isFlexPackage` tinyint(1) NOT NULL DEFAULT '0'  AFTER `numChildren`;
ALTER TABLE `package` ADD `flexNumNightsMin` int(11) NULL DEFAULT NULL  AFTER `isFlexPackage`;
ALTER TABLE `package` ADD `flexNumNightsMax` int(11) NULL DEFAULT NULL  AFTER `flexNumNightsMin`;
ALTER TABLE `package` ADD `flexNotes` text NULL DEFAULT NULL  AFTER `flexNumNightsMax`;

ALTER TABLE `pricePoint` ADD `pricePerExtraNight` double NULL DEFAULT NULL  AFTER `validityEnd`;
ALTER TABLE `pricePoint` ADD `flexRetailPricePerNight` double NULL DEFAULT NULL AFTER `pricePerExtraNight`;

ALTER TABLE `offerLuxuryLink` ADD `isFlexPackage` tinyint(1) NOT NULL DEFAULT '0'  AFTER `taxesNotIncludedDesc`;
ALTER TABLE `offerLuxuryLink` ADD `pricePerExtraNight` double NULL DEFAULT NULL  AFTER `isFlexPackage`;
ALTER TABLE `offerLuxuryLink` ADD `flexNumNightsMin` int(11) NULL DEFAULT NULL  AFTER `isFlexPackage`;
ALTER TABLE `offerLuxuryLink` ADD `flexNumNightsMax` int(11) NULL DEFAULT NULL  AFTER `flexNumNightsMin`;
ALTER TABLE `offerLuxuryLink` ADD `flexRetailPricePerNight` double NULL DEFAULT NULL AFTER `flexNumNightsMax`;

ALTER TABLE `offerFamily` ADD `isFlexPackage` tinyint(1) NOT NULL DEFAULT '0'  AFTER `taxesNotIncludedDesc`;
ALTER TABLE `offerFamily` ADD `pricePerExtraNight` double NULL DEFAULT NULL  AFTER `isFlexPackage`;
ALTER TABLE `offerFamily` ADD `flexNumNightsMin` int(11) NULL DEFAULT NULL  AFTER `isFlexPackage`;
ALTER TABLE `offerFamily` ADD `flexNumNightsMax` int(11) NULL DEFAULT NULL  AFTER `flexNumNightsMin`;
ALTER TABLE `offerFamily` ADD `flexRetailPricePerNight` double NULL DEFAULT NULL AFTER `flexNumNightsMax`;

ALTER TABLE `ticket` ADD `numNights` int(11) NULL DEFAULT NULL  AFTER `requestNotes`;

DELIMITER $$

USE `toolbox`$$

DROP VIEW IF EXISTS `pricePointOfferPreview`$$

CREATE ALGORITHM=TEMPTABLE DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `pricePointOfferPreview` AS 
SELECT `PricePoint`.`pricePointId` AS `offerId`,0 AS `rowNum`,`Package`.`packageId` AS `packageId`,`Client`.`clientId` AS `clientId`,`PricePoint`.`pricePointId` AS `pricePointId`,1 AS `offerTypeId`,1 AS `offerStatusId`,'Open' AS `offerStatusName`,`Package`.`subtitle` AS `offerSubtitle`,`Package`.`packageTitle` AS `offerName`,0 AS `isCLosed`,`Package`.`startDate` AS `startDate`,`Package`.`endDate` AS `endDate`,`PricePoint`.`retailValue` AS `retailValue`,ROUND(((`PricePoint`.`percentRetailAuc` * `PricePoint`.`retailValue`) / 100),0) AS `openingBid`,1000 AS `maxBid`,ROUND(((`PricePoint`.`percentRetailBuyNow` * `PricePoint`.`retailValue`) / 100),0) AS `buyNowPrice`,0 AS `isMystery`,NULL AS `mysteryName`,NULL AS `mysteryLocationDisplay`,NULL AS `mysteryLongDesc`,NULL AS `isPreview`,NULL AS `suppressRetail`,`Package`.`shortBlurb` AS `shortBlurb`,`Package`.`numNights` AS `roomNights`,0 AS `numBids`,0 AS `numBidders`,1 AS `isProxy`,1 AS `isAuction`,0 AS `inactive`,`Package`.`packageIncludes` AS `offerIncludes`,`Package`.`termsAndConditions` AS `termsAndConditions`,`PricePoint`.`validityDisclaimer` AS `validityDisclaimer`,`Package`.`additionalDescription` AS `additionalDescription`,20 AS `offerBidIncrement`,`Package`.`validityStartDate` AS `validityStart`,`Package`.`validityEndDate` AS `validityEnd`,NULL AS `airOffered`,0 AS `airIncluded`,`Package`.`numGuests` AS `numGuests`,NULL AS `numAdults`,NULL AS `numChildren`,`Package`.`isPrivatePackage` AS `isPrivatePackage`,`Package`.`externalOfferUrl` AS `externalOfferUrl`,`Package`.`isTaxIncluded` AS `isTaxIncluded`,`Package`.`currencyId` AS `currencyId`,`Package`.`isFlexPackage` AS `isFlexPackage`,`Package`.`flexNumNightsMin` AS `flexNumNightsMin`,`Package`.`flexNumNightsMax` AS `flexNumNightsMax`,`PricePoint`.`pricePerExtraNight` AS `pricePerExtraNight`,`PricePoint`.`flexRetailPricePerNight` AS `flexRetailPricePerNight` FROM (((`package` `Package` JOIN `pricePoint` `PricePoint` ON((`Package`.`packageId` = `PricePoint`.`packageId`))) LEFT JOIN `clientLoaPackageRel` `cl` ON((`Package`.`packageId` = `cl`.`packageId`))) LEFT JOIN `client` `Client` ON((`cl`.`clientId` = `Client`.`clientId`)))$$

DELIMITER ;

Shared:
ALTER TABLE `ticket` ADD `numNights` int(11) NULL DEFAULT NULL  AFTER `requestNotes`;

Luxurylink:
ALTER TABLE `ticket` ADD `numNights` int(11) NULL DEFAULT NULL  AFTER `requestNotes`;

DELIMITER $$

USE `luxurylink`$$

DROP VIEW IF EXISTS `offerLuxuryLink`$$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `offerLuxuryLink` AS (
SELECT `shared`.`offerLuxuryLink`.`offerId` AS `offerId`,`shared`.`offerLuxuryLink`.`primaryClient` AS `primaryClient`,`shared`.`offerLuxuryLink`.`packageId` AS `packageId`,`shared`.`offerLuxuryLink`.`clientId` AS `clientId`,`shared`.`offerLuxuryLink`.`pricePointId` AS `pricePointId`,`shared`.`offerLuxuryLink`.`offerTypeId` AS `offerTypeId`,`shared`.`offerLuxuryLink`.`offerTypeName` AS `offerTypeName`,`shared`.`offerLuxuryLink`.`offerStatusId` AS `offerStatusId`,`shared`.`offerLuxuryLink`.`offerStatusName` AS `offerStatusName`,`shared`.`offerLuxuryLink`.`offerSubtitle` AS `offerSubtitle`,`shared`.`offerLuxuryLink`.`offerName` AS `offerName`,`shared`.`offerLuxuryLink`.`isClosed` AS `isClosed`,`shared`.`offerLuxuryLink`.`startDate` AS `startDate`,`shared`.`offerLuxuryLink`.`endDate` AS `endDate`,`shared`.`offerLuxuryLink`.`numWinners` AS `numWinners`,`shared`.`offerLuxuryLink`.`retailValue` AS `retailValue`,`shared`.`offerLuxuryLink`.`openingBid` AS `openingBid`,`shared`.`offerLuxuryLink`.`maxBid` AS `maxBid`,`shared`.`offerLuxuryLink`.`buyNowPrice` AS `buyNowPrice`,`shared`.`offerLuxuryLink`.`reserveAmt` AS `reserveAmt`,`shared`.`offerLuxuryLink`.`isMystery` AS `isMystery`,`shared`.`offerLuxuryLink`.`isPreview` AS `isPreview`,`shared`.`offerLuxuryLink`.`isHoliday` AS `isHoliday`,`shared`.`offerLuxuryLink`.`isNew` AS `isNew`,`shared`.`offerLuxuryLink`.`isHidden` AS `isHidden`,`shared`.`offerLuxuryLink`.`suppressRetail` AS `suppressRetail`,`shared`.`offerLuxuryLink`.`shortBlurb` AS `shortBlurb`,`shared`.`offerLuxuryLink`.`roomGradeId` AS `roomGradeId`,`shared`.`offerLuxuryLink`.`roomGrade` AS `roomGrade`,`shared`.`offerLuxuryLink`.`roomNights` AS `roomNights`,`shared`.`offerLuxuryLink`.`numBids` AS `numBids`,`shared`.`offerLuxuryLink`.`numBidders` AS `numBidders`,`shared`.`offerLuxuryLink`.`currentRemainingPackages` AS `currentRemainingPackages`,`shared`.`offerLuxuryLink`.`isProxy` AS `isProxy`,`shared`.`offerLuxuryLink`.`isAuction` AS `isAuction`,`shared`.`offerLuxuryLink`.`inactive` AS `inactive`,`shared`.`offerLuxuryLink`.`offerIncludes` AS `offerIncludes`,`shared`.`offerLuxuryLink`.`packageBlurb` AS `packageBlurb`,`shared`.`offerLuxuryLink`.`termsAndConditions` AS `termsAndConditions`,`shared`.`offerLuxuryLink`.`validityDisclaimer` AS `validityDisclaimer`,`shared`.`offerLuxuryLink`.`additionalDescription` AS `additionalDescription`,`shared`.`offerLuxuryLink`.`offerBidIncrement` AS `offerBidIncrement`,`shared`.`offerLuxuryLink`.`validityStart` AS `validityStart`,`shared`.`offerLuxuryLink`.`validityEnd` AS `validityEnd`,`shared`.`offerLuxuryLink`.`airOffered` AS `airOffered`,`shared`.`offerLuxuryLink`.`airIncluded` AS `airIncluded`,`shared`.`offerLuxuryLink`.`numGuests` AS `numGuests`,`shared`.`offerLuxuryLink`.`maxAdults` AS `maxAdults`,`shared`.`offerLuxuryLink`.`minGuests` AS `minGuests`,`shared`.`offerLuxuryLink`.`isPopular` AS `isPopular`,`shared`.`offerLuxuryLink`.`isPrivatePackage` AS `isPrivatePackage`,`shared`.`offerLuxuryLink`.`externalOfferUrl` AS `externalOfferUrl`,`shared`.`offerLuxuryLink`.`isTaxIncluded` AS `isTaxIncluded`,`shared`.`offerLuxuryLink`.`taxesNotIncludedDesc` AS `taxesNotIncludedDesc`, `shared`.`offerLuxuryLink`.`isFlexPackage`, `shared`.`offerLuxuryLink`.`flexNumNightsMin`,`shared`.`offerLuxuryLink`.`flexNumNightsMax`, `shared`.`offerLuxuryLink`.`flexRetailPricePerNight`, `shared`.`offerLuxuryLink`.`pricePerExtraNight` FROM `shared`.`offerLuxuryLink`)$$

DELIMITER ;

Family
DELIMITER $$

USE `family`$$

DROP VIEW IF EXISTS `offerFamily`$$

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `offerFamily` AS (
SELECT `shared`.`offerFamily`.`offerId` AS `offerId`,`shared`.`offerFamily`.`primaryClient` AS `primaryClient`,`shared`.`offerFamily`.`packageId` AS `packageId`,`shared`.`offerFamily`.`clientId` AS `clientId`,`shared`.`offerFamily`.`pricePointId` AS `pricePointId`,`shared`.`offerFamily`.`offerTypeId` AS `offerTypeId`,`shared`.`offerFamily`.`offerTypeName` AS `offerTypeName`,`shared`.`offerFamily`.`offerStatusId` AS `offerStatusId`,`shared`.`offerFamily`.`offerStatusName` AS `offerStatusName`,`shared`.`offerFamily`.`offerSubtitle` AS `offerSubtitle`,`shared`.`offerFamily`.`offerName` AS `offerName`,`shared`.`offerFamily`.`isClosed` AS `isClosed`,`shared`.`offerFamily`.`startDate` AS `startDate`,`shared`.`offerFamily`.`endDate` AS `endDate`,`shared`.`offerFamily`.`numWinners` AS `numWinners`,`shared`.`offerFamily`.`retailValue` AS `retailValue`,`shared`.`offerFamily`.`openingBid` AS `openingBid`,`shared`.`offerFamily`.`maxBid` AS `maxBid`,`shared`.`offerFamily`.`buyNowPrice` AS `buyNowPrice`,`shared`.`offerFamily`.`reserveAmt` AS `reserveAmt`,`shared`.`offerFamily`.`isMystery` AS `isMystery`,`shared`.`offerFamily`.`isPreview` AS `isPreview`,`shared`.`offerFamily`.`isHoliday` AS `isHoliday`,`shared`.`offerFamily`.`isNew` AS `isNew`,`shared`.`offerFamily`.`isHidden` AS `isHidden`,`shared`.`offerFamily`.`suppressRetail` AS `suppressRetail`,`shared`.`offerFamily`.`shortBlurb` AS `shortBlurb`,`shared`.`offerFamily`.`roomGradeId` AS `roomGradeId`,`shared`.`offerFamily`.`roomGrade` AS `roomGrade`,`shared`.`offerFamily`.`roomNights` AS `roomNights`,`shared`.`offerFamily`.`numBids` AS `numBids`,`shared`.`offerFamily`.`numBidders` AS `numBidders`,`shared`.`offerFamily`.`currentRemainingPackages` AS `currentRemainingPackages`,`shared`.`offerFamily`.`isProxy` AS `isProxy`,`shared`.`offerFamily`.`isAuction` AS `isAuction`,`shared`.`offerFamily`.`inactive` AS `inactive`,`shared`.`offerFamily`.`offerIncludes` AS `offerIncludes`,`shared`.`offerFamily`.`packageBlurb` AS `packageBlurb`,`shared`.`offerFamily`.`termsAndConditions` AS `termsAndConditions`,`shared`.`offerFamily`.`validityDisclaimer` AS `validityDisclaimer`,`shared`.`offerFamily`.`additionalDescription` AS `additionalDescription`,`shared`.`offerFamily`.`offerBidIncrement` AS `offerBidIncrement`,`shared`.`offerFamily`.`validityStart` AS `validityStart`,`shared`.`offerFamily`.`validityEnd` AS `validityEnd`,`shared`.`offerFamily`.`airOffered` AS `airOffered`,`shared`.`offerFamily`.`airIncluded` AS `airIncluded`,`shared`.`offerFamily`.`numGuests` AS `numGuests`,`shared`.`offerFamily`.`maxAdults` AS `maxAdults`,`shared`.`offerFamily`.`minGuests` AS `minGuests`,`shared`.`offerFamily`.`isPopular` AS `isPopular`,`shared`.`offerFamily`.`isPrivatePackage` AS `isPrivatePackage`,`shared`.`offerFamily`.`externalOfferUrl` AS `externalOfferUrl`,`shared`.`offerFamily`.`isTaxIncluded` AS `isTaxIncluded`,`shared`.`offerFamily`.`taxesNotIncludedDesc` AS `taxesNotIncludedDesc`,`shared`.`offerFamily`.`isFlexPackage`, `shared`.`offerFamily`.`flexNumNightsMin`,`shared`.`offerFamily`.`flexNumNightsMax`, `shared`.`offerFamily`.`flexRetailPricePerNight`, `shared`.`offerFamily`.`pricePerExtraNight` FROM `shared`.`offerFamily`)$$

DELIMITER ;