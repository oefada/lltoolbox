IN conditions BLOB, IN orderByClause VARCHAR(255), IN limitClause VARCHAR(255), OUT numRecords INT

BEGIN

DECLARE curdone, mid, vid, offerId, numBids, numTickets INT DEFAULT 0;
DECLARE lastUpdate DATETIME;

DECLARE sidcur CURSOR FOR SELECT schedulingMasterId, schedulingInstanceId, offer.offerId, IF(schedulingMaster.offerTypeId IN (3, 4), COUNT(DISTINCT ticket.ticketId), COUNT(DISTINCT bid.bidId)) as numBids FROM schedulingInstance INNER JOIN schedulingMaster USING(schedulingMasterId) INNER JOIN offer USING(schedulingInstanceId) LEFT JOIN bid USING(offerId) LEFT JOIN ticket USING(offerId) WHERE schedulingInstance.startDate >= DATE_SUB(CURDATE(),INTERVAL 1 YEAR) GROUP BY schedulingInstance.schedulingInstanceId ORDER BY schedulingInstance.startDate;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET curdone = 1;

SET @conditions = conditions;
SET @order = orderByClause;
SET @limit = limitClause;

SELECT count(*) into @tableExists FROM information_schema.tables WHERE table_name = 'tmpImrTable' AND table_schema = 'fam-toolboxdev';

IF @tableExists THEN
	SELECT lastUpdateTime INTO lastUpdate FROM tmpImrTable LIMIT 1;
END IF;

IF (lastUpdate IS NULL OR lastUpdate < NOW() - INTERVAL 1 HOUR OR NOT @tableExists) THEN
	DROP TABLE IF EXISTS tmpImrTable;

	SET @lastUpdateTime = NOW();

	CREATE TABLE tmpImrTable SELECT 
			SchedulingMaster.siteId,
			SchedulingMaster.schedulingMasterId as schedulingMasterId,
                            Client.name,
			Client.clientId,
                            SchedulingMaster.overrideOfferName,
			SchedulingMaster.offerTypeId,
                            OfferType.offerTypeName,
			Package.packageId,
                            Package.numNights,
                            SchedulingMaster.retailValue,
                            SchedulingMaster.openingBid,
			SchedulingMaster.numDaysToRun,
			Package.packageName as packageName,
                            Package.validityEndDate,
			SchedulingInstance.startDate as schedulingInstanceStartDate,
			SchedulingInstance.endDate as schedulingInstanceEndDate,
			SchedulingInstance2.startDate as liveStartDate,
			SchedulingInstance2.endDate as liveEndDate,
                            IF(MAX(SchedulingInstance.endDate) > NOW(), IF(SchedulingInstance2.startDate IS NOT NULL, 'Live', 'Scheduled'), 'Not Live') as status,
                            SchedulingMaster.startDate,
                            SchedulingMaster.endDate,
                            ROUND(SchedulingMaster.openingBid / SchedulingMaster.retailValue * 100) as startingBidPercentOfRetail,
			Client.stateId,
			Client.countryId,
                            (SELECT cityName FROM city WHERE cityId = Client.cityId) as city,
                            (SELECT stateName FROM state WHERE stateId = Client.stateId) as state,
                            (SELECT countryName FROM country WHERE countryId = Client.countryId) as country,
                            Client.managerUsername,
			Loa.endDate as loaTermEnd,
			CAST(@lastUpdateTime AS DATETIME) as lastUpdateTime
                    FROM schedulingMaster AS SchedulingMaster
		 INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingMasterId = SchedulingMaster.schedulingMasterId)
                    LEFT JOIN schedulingInstance AS SchedulingInstance2 ON (SchedulingInstance2.startDate <= NOW() AND SchedulingInstance2.endDate >= NOW() AND SchedulingInstance2.schedulingMasterId = SchedulingMaster.schedulingMasterId)
		 INNER JOIN offerType AS OfferType USING(offerTypeId)
                    INNER JOIN package AS Package USING (packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
		 INNER JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
		WHERE SchedulingInstance.startDate >= DATE_SUB(CURDATE(),INTERVAL 1 YEAR)
					GROUP BY SchedulingMaster.schedulingMasterId;
	
	ALTER TABLE tmpImrTable ADD COLUMN (bidHistory text DEFAULT ""); 
	ALTER TABLE tmpImrTable ADD INDEX schedulingMasterId (schedulingMasterId); 
	ALTER TABLE tmpImrTable ADD INDEX startDate (startDate);
	ALTER TABLE tmpImrTable ADD INDEX endDate (endDate); 
	ALTER TABLE tmpImrTable ADD INDEX schedulingInstanceStartDate (schedulingInstanceStartDate);
	ALTER TABLE tmpImrTable ADD INDEX schedulingInstanceEndDate (schedulingInstanceEndDate); 

	SET @updateSql = 'UPDATE tmpImrTable SET bidHistory = CONCAT_WS(" - ", bidHistory, ?) WHERE schedulingMasterId = ?';
	
	OPEN sidcur;
        REPEAT
            FETCH sidcur INTO mid, vid, offerId, numBids;
            IF NOT curdone THEN
                BEGIN
					SET @mid = mid;
					SET @vid = vid;
					SET @numBids = numBids;
					SET @offerId = offerId;

					SET @bidHistory = CONCAT(@offerId,":",@numBids);

		#Update the bid history field
                    PREPARE stmt FROM @updateSql;
                   	EXECUTE stmt USING @bidHistory, @mid;
                    DROP PREPARE stmt;
                END;
            END IF;
        UNTIL curdone END REPEAT;
    CLOSE sidcur;

END IF;

SET @sql = concat('SELECT COUNT(*) INTO @numRecords FROM tmpImrTable AS ImrReport WHERE ', @conditions);
PREPARE stmt FROM @sql;
EXECUTE stmt;

SELECT @numRecords INTO numRecords;

DROP PREPARE stmt;



SET @sql = concat('SELECT * FROM tmpImrTable AS ImrReport WHERE ', @conditions,' ORDER BY ', @order, ' LIMIT ', @limit);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DROP PREPARE stmt;

DROP TEMPORARY TABLE IF EXISTS tmpImrTable;
END