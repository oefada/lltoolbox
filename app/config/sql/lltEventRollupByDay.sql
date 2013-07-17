CREATE TABLE `lltEventRollupByDay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lltSourceId` int(11) DEFAULT NULL,
  `lltEventId` int(11) DEFAULT NULL,
  `siteId` tinyint(3) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_sourceId_eventId_siteId_date` (`lltSourceId`,`lltEventId`,`siteId`,`startDate`) USING BTREE
) ENGINE=InnoDB CHARSET=utf8
