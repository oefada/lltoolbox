BEGIN
#Declare some basic variables to use later, mid is short for schedulingMasterId, vid is short for schedulingInstanceId
DECLARE curdone, mid, vid, numBids INT DEFAULT 0;

#Declare a cursor and handler to loop through all scheduling instances
DECLARE sidcur CURSOR FOR SELECT schedulingMasterId, schedulingInstanceId, COUNT(DISTINCT bid.bidId) as numBids FROM schedulingInstance INNER JOIN offer USING(schedulingInstanceId) INNER JOIN bid USING(offerId) GROUP BY schedulingInstance.schedulingInstanceId ORDER BY schedulingInstance.schedulingInstanceId;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET curdone = 1;

#Set some variables from the paramters passed into the stored procedure
SET @conditions = conditions;
SET @order = orderByClause;
SET @limit = limitClause;

#This temporary table will holds all of the data needed for the IMR report
DROP TEMPORARY TABLE IF EXISTS tmpImrTable;

CREATE TEMPORARY TABLE tmpImrTable SELECT 
							SchedulingMaster.schedulingMasterId as schedulingMasterId,
                            Client.name,
			Client.clientId,
                            SchedulingMaster.packageName,
			SchedulingMaster.offerTypeId,
                            OfferType.offerTypeName,
                            Package.numNights,
                            SchedulingMaster.retailValue,
                            SchedulingMaster.openingBid,
                            SchedulingMaster.validityEndDate,
                            IF(SchedulingMaster.endDate > NOW(), IF(SchedulingMaster.startDate > NOW(), 'Scheduled', 'Live'), 'Ended') as status,
                            SchedulingMaster.startDate,
                            SchedulingMaster.endDate,
                            ROUND(SchedulingMaster.openingBid / SchedulingMaster.retailValue * 100) as startingBidPercentOfRetail,
                            GROUP_CONCAT(MerchandisingFlag.merchandisingFlagName) AS merchandisingFlags,
			Client.stateId,
			Client.countryId,
                            (SELECT cityName FROM city WHERE cityId = Client.cityId) as city,
                            (SELECT stateName FROM state WHERE stateId = Client.stateId) as state,
                            (SELECT countryName FROM country WHERE countryId = Client.countryId) as country,
                            Client.teamName,
                            Client.managerUsername
                    FROM schedulingMaster AS SchedulingMaster
					INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingMasterId = SchedulingMaster.schedulingMasterId)
                    LEFT JOIN offerType AS OfferType USING(offerTypeId)
                    LEFT JOIN schedulingMasterMerchFlagRel AS SchedulingMasterMerchFlagRel ON (SchedulingMasterMerchFlagRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
                    LEFT JOIN merchandisingFlag AS MerchandisingFlag USING (merchandisingFlagId)
                    INNER JOIN package AS Package USING (packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
					GROUP BY SchedulingMaster.schedulingMasterId;
	
	ALTER TABLE tmpImrTable ADD COLUMN (bidHistory varchar(100)); #we need to add a column to hold the bidHistory
	ALTER TABLE tmpImrTable ADD INDEX schedulingMasterId (schedulingMasterId); #adding an index makes the following loop a lot faster

	SET @updateSql = 'UPDATE tmpImrTable SET bidHistory = IF(bidHistory IS NULL, ?, CONCAT(bidHistory, "-", ?)) WHERE schedulingMasterId = ?';

	#iterate through each scheduling instance, summing the bids and updating the bidHistory for each master in the IMR table
	OPEN sidcur;
        REPEAT
            FETCH sidcur INTO mid, vid, numBids;
            IF NOT curdone THEN
                BEGIN
					SET @mid = mid;
					SET @vid = vid;
					SET @numBids = numBids;
                    PREPARE stmt FROM @updateSql;
                   	EXECUTE stmt USING @numBids, @numBids, @mid;
                    DROP PREPARE stmt;
                END;
            END IF;
        UNTIL curdone END REPEAT;
    CLOSE sidcur;
DROP TABLE IF EXISTS tmpTable;
CREATE TABLE tmpTable (val BLOB);
INSERT INTO tmpTable (val) VALUES(@conditions);

SET @sql = concat('SELECT COUNT(*) INTO @numRecords FROM tmpImrTable AS ImrReport WHERE ', @conditions);
PREPARE stmt FROM @sql;
EXECUTE stmt;

SELECT @numRecords INTO numRecords;

DROP PREPARE stmt;


#now that we have everything we need in the tmp table, we can query against it
SET @sql = concat('SELECT * FROM tmpImrTable AS ImrReport WHERE ', @conditions,' ORDER BY ', @order, ' LIMIT ', @limit);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DROP PREPARE stmt;

DROP TEMPORARY TABLE IF EXISTS tmpImrTable;
END