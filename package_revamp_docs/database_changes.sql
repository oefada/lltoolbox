/* AUDRA */

ALTER TABLE `package` ADD `sites` set('luxurylink','family') NULL DEFAULT NULL  AFTER `siteId`;
UPDATE package
    SET sites = (CASE siteId WHEN 1 THEN 'luxurylink' 
    				  		 WHEN 2 THEN 'family'
    		     END);
ALTER TABLE `package` ADD `rateDisclaimer` varchar(255) NULL DEFAULT NULL  AFTER `validityDisclaimer`;
ALTER TABLE `package` ADD `rateDisclaimerDesc` varchar(255) NULL DEFAULT NULL  AFTER `rateDisclaimer`;
ALTER TABLE `package` ADD `rateDisclaimerDate` varchar(50) NULL DEFAULT NULL  AFTER `rateDisclaimerDesc`;
ALTER TABLE `packageAgeRange` ADD `packageAgeRangeId2` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `package` ADD `isBarter` tinyint(1) NOT NULL DEFAULT '1'  AFTER `taxesNotIncludedDesc`;
ALTER TABLE `logs` ADD INDEX `model` (`model`);
ALTER TABLE `logs` ADD INDEX `model_id` (`model_id`);

CREATE TABLE loaItemRatePackageRel
(loaItemRatePackageRelId INT NOT NULL AUTO_INCREMENT, 
loaItemRateId INT,
packageId INT,
numNights INT, 
PRIMARY KEY (loaItemRatePackageRelId)
);

ALTER TABLE `loaItemRatePackageRel` ADD INDEX `loaItemRateId` (`loaItemRateId`);
ALTER TABLE `loaItemRatePackageRel` ADD INDEX `packageId` (`packageId`);

INSERT INTO `loaItemType` (`loaItemTypeId`,`loaItemTypeName`,`isInclusion`,`created`,`modified`) VALUES ('21','Room Group','0',NULL,NULL);

insert into `loaItemRatePackageRel`
(loaItemRateId, packageId, numNights)
select loaItemRate.loaItemRateId, packageLoaItemRel.packageId, packageLoaItemRel.quantity
from packageLoaItemRel
inner join loaItem using (loaItemId)
inner join loaItemRatePeriod using (loaItemId)
inner join loaItemRate using (loaItemRatePeriodId)
left join loaItemRatePackageRel on loaItemRatePackageRel.loaItemRateId = loaItemRate.loaItemRateId and loaItemRatePackageRel.packageId = packageLoaItemRel.packageId
where loaItemTypeId in (1, 12, 21) and loaItemRatePackageRelId is null;

ALTER TABLE `loaItemType` ADD `sortOrder` int NULL DEFAULT NULL  AFTER `isInclusion`;

update loaItemType set sortOrder = 1 where loaItemTypeId = 19;
update loaItemType set sortOrder = 2 where loaItemTypeId = 7;
update loaItemType set sortOrder = 3 where loaItemTypeId = 6;
update loaItemType set sortOrder = 4 where loaItemTypeId = 5;
update loaItemType set sortOrder = 5 where loaItemTypeId = 16;
update loaItemType set sortOrder = 6 where loaItemTypeId = 3;
update loaItemType set sortOrder = 7 where loaItemTypeId = 15;
update loaItemType set sortOrder = 8 where loaItemTypeId = 8;
update loaItemType set sortOrder = 9 where loaItemTypeId = 17;
update loaItemType set sortOrder = 10 where loaItemTypeId = 18;


/* ARONS */

ALTER TABLE `loaItem` 
	ADD COLUMN `currencyId` int(11) unsigned   NULL after `merchandisingDescription`; 

ALTER TABLE `package` 
	ADD COLUMN `overridePackageIncludes` tinyint(1)   NULL DEFAULT '0' after `isBarter`, 
	ADD COLUMN `overrideValidityDisclaimer` tinyint(1)   NULL DEFAULT '0' after `overridePackageIncludes`; 

CREATE TABLE `packageBlackout`(
	`packageId` int(11) unsigned NOT NULL  , 
	`startDate` date NOT NULL  , 
	`endDate` date NOT NULL  , 
	`created` datetime NULL  , 
	UNIQUE KEY `packageId_2`(`packageId`,`startDate`,`endDate`) , 
	KEY `packageId`(`packageId`) 
) ENGINE=InnoDB DEFAULT CHARSET='latin1';

CREATE TABLE `packageBlackoutWeekday`(
	`packageId` int(11) unsigned NOT NULL  , 
	`weekday` set('Mon','Tue','Wed','Thu','Fri','Sat','Sun') COLLATE latin1_swedish_ci NOT NULL  , 
	`created` datetime NULL  , 
	UNIQUE KEY `packageId_2`(`packageId`) , 
	KEY `packageId`(`packageId`) 
) ENGINE=InnoDB DEFAULT CHARSET='latin1';

CREATE TABLE `packageValidityDisclaimer`(
	`packageId` int(11) unsigned NOT NULL  , 
	`startDate` date NOT NULL  , 
	`endDate` date NOT NULL  , 
	`isBlackout` tinyint(1) unsigned NULL  , 
	`created` datetime NULL  , 
	`modified` datetime NULL  , 
	UNIQUE KEY `packageId_2`(`packageId`,`startDate`,`endDate`,`isBlackout`) , 
	KEY `packageId`(`packageId`) 
) ENGINE=MyISAM DEFAULT CHARSET='latin1';



/* Alter table in target */
ALTER TABLE `packageBlackout` 
	ADD COLUMN `packageBlackoutId` int(11) unsigned   NOT NULL auto_increment first, 
	CHANGE `packageId` `packageId` int(11) unsigned   NOT NULL after `packageBlackoutId`, 
	CHANGE `startDate` `startDate` date   NOT NULL after `packageId`, 
	CHANGE `endDate` `endDate` date   NOT NULL after `startDate`, 
	CHANGE `created` `created` datetime   NULL after `endDate`, 
	ADD PRIMARY KEY(`packageBlackoutId`), COMMENT='';

/* Alter table in target */
ALTER TABLE `packageBlackoutWeekday` 
	ADD COLUMN `packageBlackoutWeekdayId` int(11) unsigned   NOT NULL auto_increment first, 
	CHANGE `packageId` `packageId` int(11) unsigned   NOT NULL after `packageBlackoutWeekdayId`, 
	CHANGE `weekday` `weekday` set('Mon','Tue','Wed','Thu','Fri','Sat','Sun')  COLLATE latin1_swedish_ci NOT NULL after `packageId`, 
	CHANGE `created` `created` datetime   NULL after `weekday`, 
	ADD PRIMARY KEY(`packageBlackoutWeekdayId`), COMMENT='';

alter table pricePoint add column validityStart date after validityDisclaimer;
alter table pricePoint add column validityEnd date after validityStart;


/* LYVAR */

ALTER TABLE `shared`.`offerLuxuryLink`     ADD COLUMN `pricePointId` INT NULL AFTER `clientId`;
ALTER TABLE `shared`.`offerFamily`     ADD COLUMN `pricePointId` INT NULL AFTER `clientId`;
ALTER TABLE `luxurylink`.`offerOpen`     ADD COLUMN `pricePointId` INT NULL AFTER `clientId`;
ALTER TABLE `family`.`offerOpen`     ADD COLUMN `pricePointId` INT NULL AFTER `clientId`;
-- redo offerLuxuryLink and offerFamily views for luxurylink and family databases,respectively.
