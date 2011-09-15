<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('OfferType', 'PaymentDetail', 'PaymentType');
	var $helpers = array('Pagination');
	var $components = array('CarDataImporter');
	//TODO: Add sorting, speed up the sql by adding indexes or a loading splash page, double check accuracy of data

	var $page;
	var $limit;
	var $perPage = 20;

	function beforeFilter() {
	    parent::beforeFilter();

		if (isset($this->params['named']['cron']) && $this->params['action'] == 'weekly_scorecard') {
			$this->LdapAuth->allow();
		}

	    $this->set('currentTab', 'reports');

	    if (!empty($this->params['named']['filter'])) {
	        $filter = urldecode($this->params['named']['filter']);
	        $get    = @unserialize($filter);

	        if ($get !== false) {
	            $this->data = $get;
	        }
	    }

	    if (@$this->data['download']['csv'] == 1) {
	        Configure::write('debug', '0');
	        $this->data['paging']['disablePagination'] = 1;

            $this->viewPath .= '/csv';
	        $this->layoutPath = 'csv';
        }

		if(@$this->data['paging']['disablePagination'] == 1) {
            $this->page = 1;
            $this->perPage = 9999;
            $this->limit = 9999;
        } elseif (!empty($this->params['named']['page'])) {
                $this->page = $this->params['named']['page'];
                $this->limit = (($this->page-1)*20).',20';
        } else {
            $this->page = 1;
            $this->limit = 20;
        }

        //None of these reports need absolutely fresh data, so perform all queries during this session in
        //no lock mode, to improve performance and not lock tables
        $this->OfferType->query('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;');
	}

	function index() {
	}

	function offer_search() {
	    if (isset($this->params['named']['offerId'])) {
	        $this->data['condition1']['field'] = 'Offer.offerId';
	        $this->data['condition1']['value'] = $this->params['named']['offerId'];
	    }
	    if (!empty($this->data)) {
	        $conditions = $this->_offer_search_build_conditions($this->data);

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Client.name';

	            $this->set('sortBy', 'Client.name');
    	        $this->set('sortDirection', 'DESC');
	        }

            $count = "SELECT COUNT(DISTINCT Offer.offerId) as numRecords
	                FROM offer AS Offer
	                LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
	                INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
	                INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
					LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
					LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
	                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                LEFT JOIN expirationCriteria AS ExpirationCriteria ON (ExpirationCriteria.expirationCriteriaId = Track.expirationCriteriaId)
	                LEFT JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

	        $sql = "SELECT
	                SchedulingInstance.schedulingInstanceId, (SchedulingInstance.endDate >= NOW()) AS offerStatus, SchedulingInstance.startDate, SchedulingInstance.endDate,
	                Client.clientId, Client.name,
	                OfferType.offerTypeId,
	                OfferType.offerTypeName,
	                Offer.offerId,
	                Package.packageId, Package.numNights, Package.approvedRetailPrice, Package.validityEndDate,
	                COUNT(Bid.bidId) as numberOfBids,
	                SchedulingMaster.schedulingMasterId, SchedulingMaster.openingBid, Package.packageName, SchedulingMaster.numDaysToRun,
	                Loa.loaId, Loa.endDate, Loa.membershipBalance,
	                (SELECT COUNT(*)
	                    FROM schedulingInstance AS SchedulingInstance2
	                    INNER JOIN schedulingMaster AS SchedulingMaster2
                        ON (SchedulingInstance2.schedulingMasterId = SchedulingMaster2.schedulingMasterId)
                        WHERE SchedulingMaster2.schedulingMasterId = SchedulingMaster.schedulingMasterId AND SchedulingInstance2.endDate >= NOW()
	                ) AS futureInstances,
	                Track.applyToMembershipBal,
	                Client.managerUsername,
	                IF((Package.validityEndDate - INTERVAL 14 DAY) <= NOW(), 3, IF((Package.validityEndDate - INTERVAL 30 DAY) <= NOW(), 2, IF((Package.validityEndDate - INTERVAL 60 DAY) <= NOW(), 1, 0))) as validityEndApproaching,
	                IF((Loa.endDate - INTERVAL 14 DAY) <= NOW(), 1, 0) as loaEndApproaching,
	                IF(SchedulingMasterPerformance.numOffersNoBid >= 10, 1, 0) as flagBids,
	                ExpirationCriteria.expirationCriteriaId,
	                ExpirationCriteria.expirationCriteriaName,
					SchedulingMaster.siteId
	                FROM offer AS Offer
	                LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
	                INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
	                INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
					LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
					LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
	                LEFT JOIN schedulingMasterPerformance AS SchedulingMasterPerformance ON (SchedulingMasterPerformance.schedulingMasterId = SchedulingMaster.schedulingMasterId)
	                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                LEFT JOIN expirationCriteria AS ExpirationCriteria ON (ExpirationCriteria.expirationCriteriaId = Track.expirationCriteriaId)
	                LEFT JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions
	                GROUP BY Offer.offerId, SchedulingMaster.schedulingMasterId
	                ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

	        foreach ($results as $k => $v) {
	            $results[$k][0]['lastInstance'] = 0;

	            $futureInstances = $this->OfferType->query("SELECT schedulingInstanceId FROM schedulingInstance AS SchedulingInstance
	                                    INNER JOIN schedulingMaster AS SchedulingMaster USING(schedulingMasterId)
	                                    INNER JOIN clientLoaPackageRel AS cl USING(packageId)
	                                    INNER JOIN loa AS Loa USING(loaId)
	                                    WHERE Loa.loaId = {$v['Loa']['loaId']}
	                                    AND SchedulingInstance.startDate > '{$v['SchedulingInstance']['endDate']}'
	                                    AND SchedulingInstance.endDate <= '{$v['Loa']['endDate']}'");

	            $last12IterationBids =  $this->OfferType->query("SELECT COUNT(Bid.bidId) AS numBids FROM bid AS Bid
	                                                        INNER JOIN offer AS Offer USING(offerId)
	                                                        INNER JOIN schedulingInstance AS SchedulingInstance USING(schedulingInstanceId)
                    	                                    INNER JOIN schedulingMaster AS SchedulingMaster USING(schedulingMasterId)
                    	                                    INNER JOIN clientLoaPackageRel AS cl USING(packageId)
                    	                                    WHERE cl.packageId = {$v['Package']['packageId']}
                    	                                    ORDER BY SchedulingInstance.endDate DESC LIMIT 12");

	            if ($last12IterationBids[0][0]['numBids'] > 0) {
	                $last8IterationBids =  $this->OfferType->query("SELECT COUNT(Bid.bidId) AS numBids FROM bid AS Bid
    	                                                        INNER JOIN offer AS Offer USING(offerId)
    	                                                        INNER JOIN schedulingInstance AS SchedulingInstance USING(schedulingInstanceId)
                        	                                    INNER JOIN schedulingMaster AS SchedulingMaster USING(schedulingMasterId)
                        	                                    INNER JOIN clientLoaPackageRel AS cl USING(packageId)
                        	                                    WHERE cl.packageId = {$v['Package']['packageId']}
                        	                                    ORDER BY SchedulingInstance.endDate DESC LIMIT 8");

                    if ($last8IterationBids[0][0]['numBids'] == 0) {
                        $results[$k][0]['iterationBidFlag'] = '#ff0';
                    }

                    //Calculate number of packages sold for this loa
                    $sql = "SELECT count(*) AS COUNT FROM ticket INNER JOIN paymentDetail pd ON (ticket.ticketId = pd.ticketId AND pd.isSuccessfulCharge = 1) INNER JOIN clientLoaPackageRel cl ON (ticket.packageId = cl.packageId) ";
            		$sql.= "WHERE cl.loaId = {$v['Loa']['loaId']} AND ticket.ticketStatusId NOT IN (7,8)";
            		$result = $this->OfferType->query($sql);

            		$numPackagesSold = $result[0][0]['COUNT'];

                    //Get all live offers for this LOA
                    $liveOffers = $this->OfferType->query("SELECT OfferLive.offerTypeId, OfferLive.openingBid, OfferLive.buyNowPrice, ExpirationCriteria.expirationCriteriaId,
                                                                    Loa.membershipBalance, Loa.numberPackagesRemaining, COUNT(Bid.bidId) as numBids
                                                                    FROM offerLuxuryLink AS OfferLive
                                                                    LEFT JOIN bid AS Bid USING(offerId)
                                                                    INNER JOIN clientLoaPackageRel as cl USING(packageId)
                                                                    INNER JOIN loa AS Loa USING(loaId)
                                                                    INNER JOIN track AS Track ON (Track.trackId = cl.trackId)
                                                                    INNER JOIN expirationCriteria AS ExpirationCriteria USING(expirationCriteriaId)
                                                                    WHERE Loa.loaId = {$v['Loa']['loaId']} AND OfferLive.isClosed = 0
                                                                    GROUP BY Loa.loaId");

                    $priceSum           = 0;

                    //calculate loa balance flags
                    foreach ($liveOffers as $k2 => $v2):
                        $currentPrice = 0;

                        //sum the opening bid or buy now price
                        if (in_array($v2['OfferLive']['offerTypeId'], unserialize(OFFER_TYPES_AUCTION))) {
                            $currentPrice = $v2['OfferLive']['openingBid'];
                            $priceSum += $v2['OfferLive']['openingBid'];
                        } elseif (in_array($v2['OfferLive']['offerTypeId'], unserialize(OFFER_TYPES_FIXED_PRICED))) {
                            $currentPrice = $v2['OfferLive']['openingBid'];
                            $priceSum += $v2['OfferLive']['buyNowPrice'];
                        }

                        //check balance (keep) status and flag
                        if ($v2['ExpirationCriteria']['expirationCriteriaId'] == 1 && $v2['Loa']['membershipBalance'] - $currentPrice <= 500) {
                            if ($v2[0]['numBids'] > 0) {
                                $results[$k][0]['loaBalanceFlag'] = 'darkred';
                            } else {
                                $results[$k][0]['loaBalanceFlag'] = 'orange';
                            }
                            break;

                        //check loa number packages and flag red if there's already a bid
                        } elseif ($v2['ExpirationCriteria']['expirationCriteriaId'] == 4 && $v2[0]['numBids'] > 0 && $v2['Loa']['loaNumberPackages'] - $numPackagesSold <= 1) {
                            $results[$k][0]['loaBalanceFlag'] = 'darkred';
                            break;
                        }
                    endforeach;

                    //do the same check but on the sum now
                    if ($liveOffers[0]['ExpirationCriteria']['expirationCriteriaId'] == 1 && $liveOffers[0]['Loa']['membershipBalance'] - $priceSum <= 500) {
                        $results[$k][0]['loaBalanceFlag'] = 'yellow';
                    } elseif ($liveOffers[0]['ExpirationCriteria']['expirationCriteriaId'] == 4
                                && $liveOffers[0]['Loa']['loaNumberPackages'] - $numPackagesSold - count($liveOffers) <= 2) {
                        $results[$k][0]['loaBalanceFlag'] = 'orange';
                    }
	            } else {
	                $results[$k][0]['iterationBidFlag'] = 'darkred';
	            }

	            if (empty($futureInstances)) {
	                $results[$k][0]['lastInstance'] = 1;
	            }
	        }

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }

	    $condition1Options = array(
	                        'SchedulingMaster.packageId' => 'Package ID',
	                        'Offer.offerId' => 'Offer ID',
	                        'Client.name' => 'Client Name',
	                        'Client.managerUsername' => 'Manager Username',
	                        'Package.packageName' => 'Offer Title'
	                        );

	    $condition3Options = array(
	                        'SchedulingInstance.liveDuring' => 'Live During Date Range',
	                        'SchedulingInstance.startDate' => 'Open Date',
	                        'SchedulingInstance.endDate' => 'Close Date'
	                        );

	    $condition4Options = $this->OfferType->find('list');
	    $this->set(compact('condition1Options', 'condition3Options', 'condition4Options'));
	}


	//TODO: take out whatever isn't report specific and put into its own method
	function _offer_search_build_conditions($data) {
	    $conditions = array();
	    foreach ($data as $k => $ca) {
	        if (isset($ca['value']['between'])) {
                $betweenCondition = $ca['value']['between'];
            } else {
                $betweenCondition = false;
            }

            /* Check if the conditions have valid data and can be used in a where clause */
	        if (empty($ca['field']) ||
	            empty($ca['value'])) {
	                continue;                               //skip if no valid data found
	            }

            /* If we got this far then that means we have adequate data for a where clause */
            if (is_array($betweenCondition)) {              //check for a condition eligible for BETWEEN
                $firstValue = array_shift($betweenCondition);
                $secondValue = array_shift($betweenCondition);

                if (strlen($firstValue) == 0) {
                    $firstValue = NULL;
                }

                if (strlen($secondValue) == 0) {
                    $secondValue = NULL;
                }

                $betweenCondition = true;
                if (!strlen($firstValue)  && !strlen($secondValue)) {   //if both between values were ommited, it's invalid
                    continue;
                }
            } else {
                unset($firstValue);
                unset($secondValue);
                $betweenCondition = false;
            }

	        if ($betweenCondition):                                    //generate valid SQL for a between condition
	            if (NULL !== $firstValue && NULL !== $secondValue) {    //if both values were entered, it's a between
	                if($ca['field'] == 'SchedulingInstance.liveDuring') {
						$liveDuringCondition = "(SchedulingInstance.startDate BETWEEN '$firstValue' AND '$secondValue' + INTERVAL 1 DAY";
			            $liveDuringCondition .= " OR SchedulingInstance.endDate BETWEEN '$firstValue' AND '$secondValue' + INTERVAL 1 DAY";
			            $liveDuringCondition .= " OR (SchedulingInstance.startDate <= '$firstValue' AND SchedulingInstance.endDate >= '$secondValue'))";

	                    $conditions[$k] = $liveDuringCondition;
	                } else if($ca['field'] == 'SchedulingInstance.startDate') {
	                    $conditions[$k] = "SchedulingInstance.startDate >= '$firstValue' AND SchedulingInstance.startDate <= '$secondValue' + INTERVAL 1 DAY";
	                } else if($ca['field'] == 'SchedulingInstance.endDate') {
                        $conditions[$k] = "SchedulingInstance.endDate >= '$firstValue' AND SchedulingInstance.endDate <= '$secondValue' + INTERVAL 1 DAY";
    	            } else {
    	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	                }
	            } else {                                                //if only one value was entered, it's not a between
	                $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	            }

	        else:
	            if(is_array($ca['value']) || ($ca['field'] == 'ExpirationCriteria.expirationCriteriaId' && $ca['value'][0] == 'keep')) {


                    //override for expiration criteria type keep
                    if ($ca['field'] == 'ExpirationCriteria.expirationCriteriaId' && $ca['value'][0] == 'keep') {
                        $values = array(1, 4);
                    } else {
                        foreach ($ca['value'] as $value) {
                            $values[] = "'{$value}'";  //wrap in single quotes
                        }
                    }
                    $conditions[$k] =   $ca['field'].' IN('.implode(',', $values).')';
                }else if ($ca['field'] == 'Package.packageName') {
	                $conditions[$k] =   "MATCH({$ca['field']}) AGAINST('{$ca['value']}' IN BOOLEAN MODE)";
	            } else if ($ca['field'] == 'Client.name') {
	                $conditions[$k] =   "{$ca['field']} LIKE '%{$ca['value']}%'";
	            } else {
	               $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }

	        endif; //end generate SQL for between condition
	    }
	    return implode($conditions, ' AND ');
	}

	function bids() {
	    if (!empty($this->data)) {
	        $conditions = $this->_bids_build_conditions($this->data);

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Offer.offerId ASC';

	            $this->set('sortBy', 'Offer.offerId');
    	        $this->set('sortDirection', 'ASC');
	        }

        $count = "    SELECT
                        Offer.offerId
                        FROM offer AS Offer
                        LEFT JOIN ticket AS Ticket ON (Ticket.offerId = Offer.offerId)
                        LEFT JOIN ticket AS Ticket2 ON (Ticket2.offerId = Offer.offerId AND Ticket2.ticketStatusId = 6)
                        LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
                        INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                        INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
						LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
						LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
                        INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                        INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                        INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                        INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                    WHERE $conditions
                    GROUP BY Offer.offerId, Client.clientId LIMIT ".$this->limit;

	        $results = $this->OfferType->query($count);
	        $numRecords = count($results);
            $numPages = ceil($numRecords / $this->perPage);

	        $sql = "SELECT
                            Offer.offerId,
                        	GROUP_CONCAT(Client.name) as clientNames,
                        	Track.expirationCriteriaId,
                        	#Track.applyToMembershipBal,
                        	OfferType.offerTypeName,
            				(SELECT Country.countryName FROM country AS Country WHERE Country.countryId = Client.countryId) AS country,
            				(SELECT State.stateName FROM state AS State WHERE State.stateId = Client.stateId) AS state,
                        	(SELECT City.cityName FROM city AS City WHERE City.cityId = Client.cityId) AS city,
                            (SchedulingMaster.openingBid / OfferLuxuryLink.retailValue * 100) AS llPercentMinBid,
                            (Ticket.billingPrice / OfferLuxuryLink.retailValue * 100) AS llPercentClose,
                            (SchedulingMaster.openingBid / OfferFamily.retailValue * 100) AS familyPercentMinBid,
                            (Ticket.billingPrice / OfferFamily.retailValue * 100) AS familyPercentClose,
                        	OfferLuxuryLink.retailValue AS llRetailValue,
                        	OfferLuxuryLink.roomNights AS llRoomNights,
                            OfferFamily.retailValue AS familyRetailValue,
                        	OfferFamily.roomNights AS familyRoomNights,
                        	SchedulingInstance.endDate,
							SchedulingMaster.siteId,
							GROUP_CONCAT(DISTINCT Ticket.ticketId) as ticketIds,
                        	COUNT(Bid.bidId) AS numBids,
                        	COUNT(DISTINCT Bid.userId) AS uniqueBids,
                        	COUNT(DISTINCT Ticket.ticketId) AS numTickets,
                        	(SELECT SUM(Ticket3.billingPrice) FROM ticket AS Ticket3 WHERE Ticket3.offerId = Offer.offerId) as moneyPotential,
                        	COUNT(DISTINCT Ticket2.ticketId) AS numTicketsCollected,
                        	(SELECT SUM(Ticket4.billingPrice) FROM ticket AS Ticket4 WHERE Ticket4.offerId = Offer.offerId AND Ticket4.ticketStatusId IN(3,4,5,6)) as moneyCollected
                    FROM offer AS Offer
                    LEFT JOIN ticket AS Ticket ON (Ticket.offerId = Offer.offerId)
                    LEFT JOIN ticket AS Ticket2 ON (Ticket2.offerId = Offer.offerId AND Ticket2.ticketStatusId IN(3,4,5,6))
                    LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
                    LEFT JOIN offerLuxuryLink AS OfferLuxuryLink ON (OfferLuxuryLink.offerId = Offer.offerId)
                    LEFT JOIN offerFamily AS OfferFamily ON (OfferFamily.offerId = Offer.offerId)
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
					LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
					LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
                    INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                    INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                    WHERE $conditions
                    GROUP BY Offer.offerId, Client.clientId
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }

	    $condition1Options = array(
	                        'SchedulingMaster.packageId' => 'Package ID',
	                        'Offer.offerId' => 'Offer ID',
	                        'Client.name' => 'Client Name',
	                        'Package.packageName' => 'Offer Title'
	                        );

	    $condition3Options = array(
	                        'SchedulingInstance.liveDuring' => 'Live During Date Range',
	                        'SchedulingInstance.startDate' => 'Open Date',
	                        'SchedulingInstance.endDate' => 'Close Date'
	                        );

	    $condition4Options = $this->OfferType->find('list');
	    $this->set(compact('condition1Options', 'condition3Options', 'condition4Options'));
	}

	function _bids_build_conditions($data) {
	    $conditions = array();
	    foreach ($data as $k => $ca) {
	        if (isset($ca['value']['between'])) {
                $betweenCondition = $ca['value']['between'];
            } else {
                $betweenCondition = false;
            }

            /* Check if the conditions have valid data and can be used in a where clause */
	        if (empty($ca['field']) ||
	            empty($ca['value'])) {
	                continue;                               //skip if no valid data found
	            }

            /* If we got this far then that means we have adequate data for a where clause */
            if (is_array($betweenCondition)) {              //check for a condition eligible for BETWEEN
                $firstValue = array_shift($betweenCondition);
                $secondValue = array_shift($betweenCondition);

                if (strlen($firstValue) == 0) {
                    $firstValue = NULL;
                }

                if (strlen($secondValue) == 0) {
                    $secondValue = NULL;
                }
                $betweenCondition = true;
                if (!strlen($firstValue)  && !strlen($secondValue)) {   //if both between values were ommited, it's invalid
                    continue;
                }
            } else {
                unset($firstValue);
                unset($secondValue);
                $betweenCondition = false;
            }

	        if ($betweenCondition):                                    //generate valid SQL for a between condition
	            if (NULL !== $firstValue && NULL !== $secondValue) {    //if both values were entered, it's a between
	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	            } else {                                                //if only one value was entered, it's not a between
	                $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	            }

	        else:
	            if(is_array($ca['value'])) {
	                //wrap in single quotes
	                foreach ($ca['value'] as $value) {
	                    $values[] = "'{$value}'";
	                }
	                $conditions[$k] =   $ca['field'].' IN('.implode(',', $values).')';
	            } else {
	                $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }

	        endif; //end generate SQL for between condition

	        //for live during we need to tweak the condition a little bit
	        if ($ca['field'] == 'SchedulingInstance.liveDuring') {
	            $originalCondition = $conditions[$k];
	            $conditions[$k] = str_replace('liveDuring', 'startDate', $originalCondition);
	            $conditions[$k] .= ' AND ';
	            $conditions[$k] .= str_replace('liveDuring', 'endDate', $originalCondition);
	        }
	    }

	    $conditions[] = 'SchedulingMaster.offerTypeId IN (1,2,6)';  //filter only auction types
	    return implode($conditions, ' AND ');
	}

	function fixed_price() {
	    if (!empty($this->data)) {
	        $conditions = $this->_fixed_price_build_conditions($this->data);

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Offer.offerId';

	            $this->set('sortBy', 'Offer.offerId');
    	        $this->set('sortDirection', 'DESC');
	        }

            $count = "SELECT COUNT(DISTINCT Ticket.ticketId) AS numRecords
                                FROM ticket AS Ticket
                                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = Ticket.offerTypeId)
                                LEFT JOIN offer AS Offer ON (Offer.offerId = Ticket.offerId)
                                LEFT JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                                LEFT JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
								LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
								LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
                                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                                LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.userId = Ticket.userId)
                    WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

	        $sql = "SELECT
                                        Offer.offerId,
                                        Ticket.ticketId,
                                        GROUP_CONCAT(Client.clientId) as clientIds,
                                    	GROUP_CONCAT(Client.name) as clientNames,
                                    	Ticket.userFirstName,
                                    	Ticket.userLastName,
                                    	Track.expirationCriteriaId,
                                    	#Track.applyToMembershipBal,
                                    	OfferType.offerTypeName,
                                    	Ticket.userCountry,
                                    	Ticket.userState,
                                    	Ticket.userCity,
                                    	Ticket.created,
										Ticket.siteId,
                                    	Ticket.billingPrice,
                                    	TicketStatus.ticketStatusName,
                                    	SUM(PaymentDetail2.paymentAmount) as moneyCollected,
                                    	IF(SUM(PaymentDetail2.paymentAmount)>=Ticket.billingPrice, MAX(PaymentDetail2.ppResponseDate), '') AS dateCollected
                                FROM ticket AS Ticket
                                LEFT JOIN ticketStatus AS TicketStatus USING (ticketStatusId)
                                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = Ticket.offerTypeId)
                                LEFT JOIN offer AS Offer ON (Offer.offerId = Ticket.offerId)
                                LEFT JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                                LEFT JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
								LEFT JOIN schedulingMasterTrackRel AS SchedulingMasterTrackRel ON (SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId)
								LEFT JOIN track AS Track ON (Track.trackId = SchedulingMasterTrackRel.trackId)
                                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                                LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.userId = Ticket.userId)
                                LEFT JOIN paymentDetail AS PaymentDetail2 ON (PaymentDetail2.paymentDetailId = PaymentDetail.paymentDetailId AND PaymentDetail2.isSuccessfulCharge = 1)
                    WHERE $conditions
                    GROUP BY Ticket.ticketId
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }
	}

	function _fixed_price_build_conditions($data) {
	    $conditions = array();
	    foreach ($data as $k => $ca) {
	        if (isset($ca['value']['between'])) {
                $betweenCondition = $ca['value']['between'];
            } else {
                $betweenCondition = false;
            }

            /* Check if the conditions have valid data and can be used in a where clause */
	        if (empty($ca['field']) ||
	            empty($ca['value'])) {
	                continue;                               //skip if no valid data found
	            }

            /* If we got this far then that means we have adequate data for a where clause */
            if (is_array($betweenCondition)) {              //check for a condition eligible for BETWEEN
                $firstValue = array_shift($betweenCondition);
                $secondValue = array_shift($betweenCondition);

                if (strlen($firstValue) == 0) {
                    $firstValue = NULL;
                }

                if (strlen($secondValue) == 0) {
                    $secondValue = NULL;
                }
                $betweenCondition = true;
                if (!strlen($firstValue)  && !strlen($secondValue)) {   //if both between values were ommited, it's invalid
                    continue;
                }
            } else {
                unset($firstValue);
                unset($secondValue);
                $betweenCondition = false;
            }

	        if ($betweenCondition):                                    //generate valid SQL for a between condition
	            if (NULL !== $firstValue && NULL !== $secondValue) {    //if both values were entered, it's a between
	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	            } else {                                                //if only one value was entered, it's not a between
	                $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	            }

	        else:
	            if(is_array($ca['value'])) {
	                //wrap in single quotes
	                foreach ($ca['value'] as $value) {
	                    $values[] = "'{$value}'";
	                }
					if ($ca['field'] == 'Track.expirationCriteriaId') {
						$valTmp = $values;
						$values = array();
						foreach($valTmp as $val) {
							if ($val == "'keep'") {
								$values[] = '1,4';
							} elseif($val == "'remit'") {
								$values[] = '2,3';
							}
						}
					}
	                $conditions[$k] =   $ca['field'].' IN('.implode(',', $values).')';
	            } else {
	                $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }

	        endif; //end generate SQL for between condition
	    }

	    $conditions[] = 'Ticket.formatId = 2';  //filter only fixed price types
	    return implode($conditions, ' AND ');
	}


	private function getOrderBy($sortBy){

		$orderBy='';
		if ($sortBy=="membershipFee")$orderBy.="ORDER BY Loa.membershipFee ";
		else if ($sortBy=="loaEndDate")$orderBy.="ORDER BY loaEndDate ";
		else if ($sortBy=="Loa.startDate")$orderBy.="ORDER BY Loa.startDate ";
		else if ($sortBy=="age")$orderBy.="ORDER BY age ";
		else if ($sortBy=="Client.clientId")$orderBy.="ORDER BY Client.clientId ";
		else if ($sortBy=="Client.name")$orderBy.="ORDER BY Client.name ";
		else if ($sortBy=="Client.managerUsername")$orderBy.="ORDER BY Client.managerUsername ";
		else if ($sortBy=="Loa.loaId")$orderBy.="ORDER BY Loa.loaId ";
		else if ($sortBy=="membershipBalance")$orderBy.="ORDER BY membershipBalance ";
		else if ($sortBy=="lastSellPrice")$orderBy.="ORDER BY lastSellPrice ";
		else if ($sortBy=="lastSellDate")$orderBy.="ORDER BY lastSellDate ";
		else if ($sortBy=="Loa.notes")$orderBy.="ORDER BY Loa.notes ";

		return $orderBy;

	}

	private function getNumOffers($results,$sortBy,$sortDirection){

		$clientId_arr=array();
		foreach($results as $key=>$arr){
			$sites_arr=explode(",",$arr['Client']['sites']);
			foreach($sites_arr as $site){
				$clientId_arr[$site][]=$arr['Client']['clientId'];
			}
		}
// TODO: move these queries to a model in the way the rest of this controller didn't
		$ll_num_arr=array();
		$q="SELECT clientId,count(clientId) as num FROM offerLuxuryLink ";
		$q.="WHERE clientId IN (".implode(",",$clientId_arr['luxurylink']).") ";
		$q.="AND endDate>NOW() ";
		$q.="AND isClosed=0 ";
		$q.="GROUP BY clientId";
		$rows=$this->OfferType->query($q);
		foreach($rows as $key=>$arr){
			$clientId=$arr['offerLuxuryLink']['clientId'];
			$num=$arr[0]['num'];
			$ll_num_arr[$clientId]=$num;
		}
		$fg_num_arr=array();
		$q="SELECT clientId,count(clientId) as num FROM offerLuxuryLink ";
		$q.="WHERE clientId IN (".implode(",",$clientId_arr['family']).") ";
		$q.="AND endDate>NOW() ";
		$q.="AND isClosed=0 ";
		$q.="GROUP BY clientId";
		$rows=$this->OfferType->query($q);
		foreach($rows as $key=>$arr){
			$clientId=$arr['offerLuxuryLink']['clientId'];
			$num=$arr[0]['num'];
			$fg_num_arr[$clientId]=$num;
		}

		foreach($results as $key=>$arr){

			$sites_arr=explode(",",$arr['Client']['sites']);
			foreach($ll_num_arr as $clientId=>$numOffers){
				if ($clientId==$results[$key]['Client']['clientId'] && in_array("luxurylink",$sites_arr)){
					$results[$key]['Client']['numOffers']=" LL($numOffers)";
					$ll_clientId_numOffers_arr[$clientId]=$numOffers;
				}
			}
			foreach($fg_num_arr as $clientId=>$numOffers){
				if ($clientId==$results[$key]['Client']['clientId'] && in_array("family",$sites_arr)){
					if (isset($results[$key]['Client']['numOffers'])){
						$results[$key]['Client']['numOffers'].=" FG($numOffers)";
					}else{
						$results[$key]['Client']['numOffers']="FG($numOffers)";
					}
					$fg_clientId_numOffers_arr[$clientId]=$numOffers;
				}
			}

		}

		return $results;

	}

	function aging() {

		$sortBy=isset($this->params['named']['sortBy'])?$this->params['named']['sortBy']:false;
		$sortDirection=isset($this->params['named']['sortDirection'])?$this->params['named']['sortDirection']:"DESC";

		if (isset($_GET['showOld'])) {
			$condition = '';
		} else {
			$condition = " AND Loa.endDate >= NOW() - INTERVAL 30 DAY";
		}

		$sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
																Loa.loaId,
																Loa.startDate,
																MAX(Loa.endDate) AS loaEndDate,
																Loa.membershipFee, Loa.membershipBalance, Loa.notes,
							Loa.membershipTotalPackages,
							Loa.membershipPackagesRemaining,
							Loa.loaMembershipTypeId,
																DATEDIFF(NOW(), Loa.startDate) as age,
							Client.managerUsername, Client.locationDisplay,
							(SELECT Ticket.billingPrice
								FROM ticket as Ticket
								INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
								WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
								ORDER BY Ticket.created DESC
								LIMIT 1
							) as lastSellPrice,
							(SELECT Ticket.created
								FROM ticket as Ticket
								INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
								WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
								ORDER BY Ticket.created DESC
								LIMIT 1
							) as lastSellDate
													FROM client AS Client
													INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
								WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -31 DAY) AND NOW()
						AND YEAR(Loa.endDate) >= YEAR(NOW() - INTERVAL 1 YEAR) AND Loa.loaLevelId = 2 $condition
													GROUP BY Client.clientId, Loa.loaId ";
		//													ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";
		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;
	  $results['0 to 30'] = $this->OfferType->query($sql);
		$results['0 to 30'] = $this->getNumOffers($results['0 to 30'],$sortBy,$sortDirection);



    $sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance, Loa.notes,
										Loa.membershipTotalPackages,
										Loa.membershipPackagesRemaining,
										Loa.loaMembershipTypeId,
            	                        DATEDIFF(NOW(), Loa.startDate) as age,
										Client.managerUsername, Client.locationDisplay,
									(SELECT Ticket.billingPrice
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellPrice,
									(SELECT Ticket.created
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellDate
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -61 DAY) AND DATE_ADD(NOW(), INTERVAL -31 DAY)
									AND YEAR(Loa.endDate) >= YEAR(NOW() - INTERVAL 1 YEAR) AND Loa.loaLevelId = 2 $condition
                                GROUP BY Client.clientId, Loa.loaId ";
    //                            ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";

		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;
		$results['31 to 60'] = $this->OfferType->query($sql);
		$results['31 to 60'] = $this->getNumOffers($results['31 to 60'],$sortBy,$sortDirection);

		$sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance, Loa.notes,
										Loa.membershipTotalPackages,
										Loa.membershipPackagesRemaining,
										Loa.loaMembershipTypeId,
            	                        DATEDIFF(NOW(), Loa.startDate) as age,
										Client.managerUsername, Client.locationDisplay,
									(SELECT Ticket.billingPrice
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellPrice,
									(SELECT Ticket.created
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellDate
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -91 DAY) AND DATE_ADD(NOW(), INTERVAL -61 DAY)
									AND YEAR(Loa.endDate) >= YEAR(NOW() - INTERVAL 1 YEAR) AND Loa.loaLevelId = 2 $condition
                                GROUP BY Client.clientId, Loa.loaId ";
    //                            ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";
		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;
		$results['61 to 90'] = $this->OfferType->query($sql);
		$results['61 to 90'] = $this->getNumOffers($results['61 to 90'],$sortBy,$sortDirection);

		$sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance, Loa.notes,
										Loa.membershipTotalPackages,
										Loa.membershipPackagesRemaining,
										Loa.loaMembershipTypeId,
            	                        DATEDIFF(NOW(), Loa.startDate) as age,
										Client.managerUsername, Client.locationDisplay,
									(SELECT Ticket.billingPrice
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellPrice,
									(SELECT Ticket.created
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellDate
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -121 DAY) AND DATE_ADD(NOW(), INTERVAL -91 DAY)
									AND YEAR(Loa.endDate) >= YEAR(NOW() - INTERVAL 1 YEAR) AND Loa.loaLevelId = 2 $condition
                                GROUP BY Client.clientId, Loa.loaId ";
    //                            ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";
		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;
		$results['91 to 120'] = $this->OfferType->query($sql);
		$results['91 to 120'] = $this->getNumOffers($results['91 to 120'],$sortBy,$sortDirection);

		$sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance, Loa.notes,
										Loa.membershipTotalPackages,
										Loa.membershipPackagesRemaining,
										Loa.loaMembershipTypeId,
            	                        DATEDIFF(NOW(), Loa.startDate) as age,
										Client.managerUsername, Client.locationDisplay,
									(SELECT Ticket.billingPrice
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellPrice,
									(SELECT Ticket.created
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellDate
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -181 DAY) AND DATE_ADD(NOW(), INTERVAL -121 DAY)
									AND YEAR(Loa.endDate) >= YEAR(NOW() - INTERVAL 1 YEAR) AND Loa.loaLevelId = 2 $condition
                                GROUP BY Client.clientId, Loa.loaId ";
    //                            ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";

		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;
		$results['121 to 180'] = $this->OfferType->query($sql);
		$results['121 to 180'] = $this->getNumOffers($results['121 to 180'],$sortBy,$sortDirection);

		$sql = "SELECT Client.clientId, Client.name, Client.sites as sites,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance, Loa.notes,
										Loa.membershipTotalPackages,
										Loa.membershipPackagesRemaining,
										Loa.loaMembershipTypeId,
            	                        DATEDIFF(NOW(), Loa.startDate) as age,
										Client.managerUsername, Client.locationDisplay,
									(SELECT Ticket.billingPrice
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellPrice,
									(SELECT Ticket.created
										FROM ticket as Ticket
										INNER JOIN clientLoaPackageRel clp ON(clp.packageId = Ticket.packageId)
										WHERE clp.clientId = Client.clientId AND clp.LoaId = Loa.loaId
										ORDER BY Ticket.created DESC
										LIMIT 1
									) as lastSellDate
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE (Loa.membershipBalance > 0 OR Loa.membershipPackagesRemaining > 0) AND Loa.startDate <= DATE_ADD(NOW(), INTERVAL - 181 DAY)
									AND Loa.endDate >= NOW() AND Loa.loaLevelId = 2 $condition
                                GROUP BY Client.clientId, Loa.loaId ";
    //                            ORDER BY Loa.startDate ASC, Loa.membershipBalance DESC";
		$sql.=$this->getOrderBy($sortBy);
		$sql.=$sortDirection;

		$results['180 plus'] = $this->OfferType->query($sql);
		$results['180 plus'] = $this->getNumOffers($results['180 plus'],$sortBy,$sortDirection);

		$this->set('showingOld', isset($_GET['showOld']) ? 1 : 0);
	  $this->set('results', $results);

	}

	function auction_timeslot() {
	    if (!empty($this->data)) {
	        $conditions = $this->_bids_build_conditions($this->data); //we can use the same conditions as bids

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Offer.offerId';

	            $this->set('sortBy', 'Offer.offerId');
    	        $this->set('sortDirection', 'DESC');
	        }

	        $sql = "SELECT HOUR(SchedulingInstance.endDate), DATE_FORMAT(SchedulingInstance.endDate, '%Y-%m-%d') as onlyEndDate, OfferType.offerTypeName, COUNT(DISTINCT SchedulingInstance.schedulingInstanceId) as numOffers,
                        CASE
                            WHEN HOUR(SchedulingInstance.endDate) BETWEEN 0 AND 6 THEN -1 #before 7am
                                WHEN HOUR(SchedulingInstance.endDate) BETWEEN 7 AND 16 THEN HOUR(SchedulingInstance.endDate) #everything in between
                                WHEN HOUR(SchedulingInstance.endDate) BETWEEN 17 AND 24 THEN 999 #after 5pm
                            END as timeOfDay,
					SchedulingMaster.siteId
                    FROM offer AS Offer
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
                    LEFT JOIN offerType AS OfferType ON (SchedulingMaster.offerTypeId = OfferType.offerTypeId)
                    WHERE $conditions
                    GROUP BY SchedulingMaster.siteId, onlyEndDate, timeOfDay, OfferType.offerTypeId
                    ORDER BY onlyEndDate, timeOfDay ASC";

	        $results = $this->OfferType->query($sql);

	        //have to get the results in a format that we can easily loop through
			$siteIds = $this->siteIds;
	        $rows = array();
	        foreach ($results as $r) {
	            $rows[$r[0]['onlyEndDate'].' '.$siteIds[$r['SchedulingMaster']['siteId']]][$r['OfferType']['offerTypeName']][$r[0]['timeOfDay']] = $r[0]['numOffers'];
	        }

            $this->set('data', $this->data);
	        $this->set('results', $rows);
	        $this->set('serializedFormInput', serialize($this->data));
	    }
	}

	function check_in_date() {
	    if (!empty($this->data)) {
	        $conditions = $this->_build_conditions($this->data);

			$clientId = (isset($this->data['Client']['clientId']) && !empty($this->data['Client']['clientId'])) ? (int)$this->data['Client']['clientId'] : false;
			$clientSql = '';
			if ($clientId !== false) {
				$clientSql = 'AND Client.clientId = ' . $clientId;
			}

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Reservation.arrivalDate DESC';

	            $this->set('sortBy', 'Reservation.arrivalDate');
    	        $this->set('sortDirection', 'DESC');
	        }

            if (empty($conditions)) {
                $conditions = '1=1';
            }

            $sql = "SELECT COUNT(DISTINCT Reservation.ticketId) as numRecords
						FROM reservation AS Reservation
						INNER JOIN ticket AS Ticket USING (ticketId)
						INNER JOIN user AS User ON User.userId = Ticket.userId
						INNER JOIN userSiteExtended AS UserSiteExtended ON UserSiteExtended.userId = User.userId
						INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
						INNER JOIN client AS Client ON(Client.clientId = ClientLoaPackageRel.clientId) $clientSql
						LEFT JOIN ticketRefund AS TicketRefund ON TicketRefund.ticketId = Ticket.ticketId
						WHERE TicketRefund.ticketRefundId IS NULL AND $conditions";

    	    $results = $this->OfferType->query($sql);
    	    $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

			$sql = "SELECT Reservation.*,
						GROUP_CONCAT(DISTINCT Client.clientId) as clientIds,
						GROUP_CONCAT(DISTINCT Client.name) as clientNames,
						UserSiteExtended.username,
						Ticket.siteId,
						Ticket.userFirstName,
						Ticket.userLastName,
						Ticket.billingPrice
					FROM reservation AS Reservation
					INNER JOIN ticket AS Ticket USING (ticketId)
					INNER JOIN user AS User ON User.userId = Ticket.userId
					INNER JOIN userSiteExtended AS UserSiteExtended ON UserSiteExtended.userId = User.userId
					INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
					INNER JOIN client AS Client ON(Client.clientId = ClientLoaPackageRel.clientId) $clientSql
					LEFT JOIN ticketRefund AS TicketRefund ON TicketRefund.ticketId = Ticket.ticketId
	   				WHERE TicketRefund.ticketRefundId IS NULL AND $conditions
                    GROUP BY Ticket.ticketId
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
		    $this->set('serializedFormInput', serialize($this->data));
	    }
	}

	function auction_winner() {
	    if (!empty($this->data)) {
	        $conditions = $this->_build_conditions($this->data);

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Ticket.ticketId DESC';

	            $this->set('sortBy', 'Ticket.ticketId');
    	        $this->set('sortDirection', 'DESC');
	        }

            if (empty($conditions)) {
                $conditions = '1=1';
            }

            $sql = "SELECT COUNT(DISTINCT Ticket.ticketId) as numRecords
                        FROM ticket AS Ticket
                               INNER JOIN offer AS Offer USING(offerId)
                               LEFT JOIN offerType AS OfferType USING(offerTypeId)
                               INNER JOIN schedulingInstance AS SchedulingInstance USING(schedulingInstanceId)
							   INNER JOIN schedulingMaster AS SchedulingMaster ON SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId
							   INNER JOIN schedulingMasterTrackRel as SchedulingMasterTrackRel ON SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId
                               LEFT JOIN track AS Track ON Track.trackId = SchedulingMasterTrackRel.trackId
                               LEFT JOIN paymentDetail AS PaymentDetail USING (ticketId)
                               LEFT JOIN paymentProcessor AS PaymentProcessor USING (paymentProcessorId)
                               LEFT JOIN userPaymentSetting AS UserPaymentSetting ON (UserPaymentSetting.userPaymentSettingId = PaymentDetail.userPaymentSettingId)
                               INNER JOIN package AS Package ON Package.packageId = Ticket.packageId
                               INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                               INNER JOIN client as Client ON(Client.clientId = ClientLoaPackageRel.clientId)
                        WHERE $conditions";

    	    $results = $this->OfferType->query($sql);
    	    $numRecords = $results[0][0]['numRecords'];
          $numPages = ceil($numRecords / $this->perPage);

					// query modified
					// mbyrnes 2011-02-22
					// The join on pricePoint caused problems because there is no ticketId in the pricePoint table
					// The result was multiple rows incorrectly selected and SUM-med before the GROUP BY ticketId
					// was enforced
					// Too much redundant data in the tables and not enough correlated keys to get it right easily,
					// so php solution implemented
          $sql = "SELECT if(Ticket.offerTypeId IN(3,4),PaymentDetail.ppResponseDate,SchedulingInstance.endDate) endDate,
                           PaymentDetail.ppResponseDate,
                           Ticket.ticketId,
                           GROUP_CONCAT(DISTINCT Client.clientId) as clientIds,
                           GROUP_CONCAT(DISTINCT Client.oldProductId) as oldProductIds,
                           GROUP_CONCAT(DISTINCT Client.name) as clientNames,
                           Ticket.userFirstName,
                           Ticket.userLastName,
                           PaymentDetail.ppBillingAddress1,
                           PaymentDetail.ppBillingCity,
                           PaymentDetail.ppBillingState,
                           PaymentDetail.ppBillingCountry,
                           PaymentDetail.ppBillingZip,
                           Ticket.userWorkPhone,
                           Ticket.userHomePhone,
                           Ticket.userMobilePhone,
                           Ticket.userEmail1,
                           Ticket.numNights,
													 r.arrivalDate,
													 Ticket.siteId,
                           PaymentDetail.ccType,
                           PaymentDetail.ppCardNumLastFour,
                           PaymentDetail.ppExpMonth,
                           PaymentDetail.ppExpYear,
                           SUM(PaymentDetail.ppBillingAmount) as revenue,
                           OfferType.offerTypeName,

						   IF(Ticket.siteId = 2, ROUND((SUM(PaymentDetail.ppBillingAmount) / (offerFamily.retailValue + IF(offerFamily.isFlexPackage = 1, (Ticket.numNights - offerFamily.roomNights) * offerFamily.flexRetailPricePerNight, 0)) * 100))
						                       , ROUND((SUM(PaymentDetail.ppBillingAmount) / (offerLuxuryLink.retailValue + IF(offerLuxuryLink.isFlexPackage = 1, (Ticket.numNights - offerLuxuryLink.roomNights) * offerLuxuryLink.flexRetailPricePerNight, 0)) * 100))
						   ) AS percentOfRetail,


                           PaymentProcessor.paymentProcessorName,
                           ExpirationCriteria.expirationCriteriaId,
													 SchedulingMaster.pricePointId,
													 SchedulingMaster.packageId,
						   Promo.amountOff,
						   PromoCode.promoCode,
                           Package.numNights,
                           Ticket.offerId,
                           Ticket.guaranteeAmt,
                           Ticket.billingPrice
                    FROM ticket AS Ticket
                           INNER JOIN offer AS Offer USING(offerId)
                           LEFT JOIN offerType AS OfferType ON (Ticket.offerTypeId = OfferType.offerTypeId)
                           LEFT JOIN schedulingInstance AS SchedulingInstance USING(schedulingInstanceId)
						   INNER JOIN schedulingMaster AS SchedulingMaster ON SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId
						   INNER JOIN schedulingMasterTrackRel as SchedulingMasterTrackRel ON SchedulingMasterTrackRel.schedulingMasterId = SchedulingMaster.schedulingMasterId
                           LEFT JOIN track AS Track ON Track.trackId = SchedulingMasterTrackRel.trackId
                           LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge <> 0)
                           LEFT JOIN paymentProcessor AS PaymentProcessor USING (paymentProcessorId)
                           LEFT JOIN userPaymentSetting AS UserPaymentSetting ON (UserPaymentSetting.userPaymentSettingId = PaymentDetail.userPaymentSettingId)
                           LEFT JOIN package AS Package ON Package.packageId = Ticket.packageId
                           LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                           LEFT JOIN client as Client ON(Client.clientId = ClientLoaPackageRel.clientId)
                           LEFT JOIN expirationCriteria AS ExpirationCriteria USING(expirationCriteriaId)
       					   LEFT JOIN reservation r ON Ticket.ticketId = r.ticketId
						   LEFT JOIN promoTicketRel ptr ON Ticket.ticketId = ptr.ticketId
						   LEFT JOIN promoCode PromoCode ON ptr.promoCodeId = PromoCode.promoCodeId
						   LEFT JOIN promoCodeRel pcr ON PromoCode.promoCodeId = pcr.promoCodeId
						   LEFT JOIN promo Promo ON pcr.promoId = Promo.promoId
						   LEFT JOIN offerLuxuryLink USING(offerId)
						   LEFT JOIN offerFamily USING(offerId)
	   				WHERE $conditions
                    GROUP BY Ticket.ticketId
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

	        $this->PaymentDetail->recursive = 0;
	        $ids = null;
	        foreach($results as $k => $v) {
	        	if(!$ids) {
	        		$ids = $v['Ticket']['ticketId'];
	        	} else {
	        		$ids .= ','.$v['Ticket']['ticketId'];
	        	}
	        	$paymentDetail = $this->PaymentDetail->query('
	        		SELECT pd.*, pt.paymentTypeName FROM paymentDetail AS pd
	        		INNER JOIN paymentType AS pt ON pt.paymentTypeId = pd.paymentTypeId
	        		WHERE ticketId = '.$v['Ticket']['ticketId']);
	        	$results[$k]['PaymentDetailFull'] = $paymentDetail;
	        }
					// This extracts pricePointId and PackageId from the result set and queries
					// pricePoint table to get the validity dates and then inserts them into the $results array
					// 2011-02-22 mbyrnes
					foreach($results as $key=>$arr){
						$ticketId=$arr['Ticket']['ticketId'];
						$pricePointId_arr[$ticketId]=$arr['SchedulingMaster']['pricePointId'];
						$packageId_arr[$ticketId]=$arr['SchedulingMaster']['packageId'];
					}

					$q="SELECT validityStart,validityEnd,pricePointId,packageId ";
					$q.="FROM pricePoint WHERE pricePointId IN (".implode(",",$pricePointId_arr).") AND ";
					$q.="packageId IN (".implode(",",$packageId_arr).")";
					$pp_results=$this->OfferType->query($q);
					foreach($pp_results as $i=>$pp_arr){

						foreach($results as $j=>$r_arr){

							if ($pp_arr['pricePoint']['pricePointId']==$r_arr['SchedulingMaster']['pricePointId']
							&& $pp_arr['pricePoint']['packageId']==$r_arr['SchedulingMaster']['packageId']){
								$results[$j]['PricePoint']['validityStart']=$pp_arr['pricePoint']['validityStart'];
								$results[$j]['PricePoint']['validityEnd']=$pp_arr['pricePoint']['validityEnd'];

							}

						}

					}

					// 2011-04-25 jwoods - need reserveAmt for "guarantee" ticket 1917
					foreach($results as $key=>$arr) {
						$guaranteeTable = ($arr['Ticket']['siteId'] == 1) ? 'offerLuxuryLink' : 'offerFamily';
						$q="SELECT reserveAmt FROM " . $guaranteeTable . " WHERE offerId = " . $arr['Ticket']['offerId'];
						$guar_results=$this->OfferType->query($q);
						$results[$key]['OfferLookup']['reserveAmt']=$guar_results[0][$guaranteeTable]['reserveAmt'];
					}


					// populate guarantee amount
					foreach($results as $key=>$arr) {
					    $guaranteeAmount = 0;
					    $guaranteeTicket = $arr['Ticket']['guaranteeAmt'];
					    $guaranteeReserveAmt = $arr['OfferLookup']['reserveAmt'];
					    if ((intval($guaranteeTicket) > 0) && ($arr['Ticket']['billingPrice'] < $guaranteeTicket)) {
					        $guaranteeAmount = $guaranteeTicket;
					    } elseif ((intval($guaranteeReserveAmt) > 0) && ($arr['Ticket']['billingPrice'] < $guaranteeReserveAmt)) {
					        $guaranteeAmount = $guaranteeReserveAmt;
					    }
					    $results[$key]['OfferLookup']['guaranteeAmount'] = $guaranteeAmount;
					}


				$this->set('currentPage', $this->page);
				$this->set('numRecords', $numRecords);
				$this->set('numPages', $numPages);
				$this->set('data', $this->data);
				$this->set('results', $results);
				$this->set('serializedFormInput', serialize($this->data));

	    }
	}

	function cmr() {
	    if (!empty($this->data) || !empty($this->params['named']['clientIds'])) {
	        if ($this->data['condition2']['value'] == 'keep') {
	            $this->data['condition2']['value'] = array(1, 4);
	        }

			if (!empty($this->params['named']['clientIds'])) {
				$this->page = 1;
	            $this->perPage = 9999;
	            $this->limit = 9999;
				$conditions = "Client.clientId IN({$this->params['named']['clientIds']})";
			} else {
		        $conditions = $this->_build_conditions($this->data);
			}

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Loa.loaId';

	            $this->set('sortBy', 'Loa.loaId');
    	        $this->set('sortDirection', 'DESC');
	        }

            if (empty($conditions)) {
                $conditions = '1=1';
            }

            switch ( $this->data['condition2']['value'] ):
                case 1:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (1,2,6) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers > 0';
                    break;
                case 2:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (3,4) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers > 0';
                    break;
                case 3:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (1,2,6) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers = 0';
                    break;
                case 4:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (3,4) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers = 0';
                    break;
                case 5:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers = 0';
                    break;
                case 6:
                    $subquery = '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (1,2,6) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $subquery .= '(SELECT COUNT(*) FROM offerLuxuryLink as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (3,4) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers2,';
                    $having = 'HAVING countSubqueryOffers > 0 AND countSubqueryOffers2 > 0';
                    break;
				default:
					$subquery = '';
					$having = '';
            endswitch;

            $sql = "SELECT $subquery 1
                        FROM client as Client
                        INNER JOIN loa as Loa USING(clientId)
                        LEFT JOIN loaLevel as LoaLevel USING(loaLevelId)
						LEFT JOIN multiSite as MultiSite ON(MultiSite.model = 'Client' AND MultiSite.modelId = Client.clientId)
                        WHERE Loa.endDate >= NOW() AND $conditions
                        GROUP BY Loa.loaId, Client.clientId
                        $having";

    	    $results = $this->OfferType->query($sql);
    	    $numRecords = count($results);
            $numPages = ceil($numRecords / $this->perPage);

            $sql = "SELECT
                        $subquery
                        Client.clientId,
                        Client.name,
                        LoaLevel.loaLevelName,
                        Loa.endDate,
                        Loa.loaNumberPackages,
                        #remit packages sold
                        #remit packages left
                        Loa.upgraded,
                        Loa.totalRemitted,
                        (SELECT cityName from city where cityId = Client.cityId) as city,
                        (SELECT stateName from state where stateId = Client.stateId) as state,
                        (SELECT countryName from country where countryId = Client.countryId) as country,
                        Loa.loaId,
                        Loa.clientId,
                        Loa.membershipBalance,
                        Loa.membershipFee,
                        Loa.startDate,
                        Loa.loaNumberPackages,
                        Loa.numberPackagesRemaining,
                        DATEDIFF(NOW(), Loa.startDate) as loaNumberOfDaysActive,
                        ROUND( (Loa.totalRevenue / DATEDIFF(Loa.endDate, Loa.startDate)), 2) as dailyMembershipFee,
                        ROUND( (Loa.totalRevenue - Loa.membershipBalance) / (Loa.totalRevenue / DATEDIFF(Loa.endDate, Loa.startDate)) ) as numDaysPaid,
                        (Loa.startDate + INTERVAL ( (Loa.totalRevenue - Loa.membershipBalance) / (Loa.totalRevenue / DATEDIFF(Loa.endDate, Loa.startDate)) ) DAY) as paidThru,
                        DATEDIFF(Loa.endDate, (Loa.startDate + INTERVAL ( (Loa.totalRevenue - Loa.membershipBalance) / (Loa.totalRevenue / DATEDIFF(Loa.endDate, Loa.startDate)) ) DAY)) as daysBehindSchedule,
                        Client.managerUsername,
						MultiSite.sites
                    FROM client as Client
                    INNER JOIN loa as Loa ON(Loa.clientId = Client.clientId AND Loa.inactive != 1)
                    LEFT JOIN loaLevel as LoaLevel USING(loaLevelId)
					LEFT JOIN multiSite as MultiSite ON(MultiSite.model = 'Client' AND MultiSite.modelId = Client.clientId)
                    WHERE Loa.endDate >= NOW() AND $conditions
                    GROUP BY Loa.loaId, Client.clientId
                    $having
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }
	}

	function imr() {
	    if (!empty($this->data) ||
			!empty($this->params['named']['schedulingMasterIds']) ||
			!empty($this->params['named']['packageIds'])) {

	        if(isset($this->data['condition1'])) {
	            $liveDuringStartDate  = $this->data['condition1']['value']['between'][0];
	            $liveDuringEndDate    = $this->data['condition1']['value']['between'][1];

	            $condition1Saved = $this->data['condition1'];
	            unset($this->data['condition1']);
	        }

			if (!empty($this->params['named']['schedulingMasterIds'])) {
				$this->page = 1;
	            $this->perPage = 9999;
	            $this->limit = 9999;
				$conditions = "schedulingMasterId IN({$this->params['named']['schedulingMasterIds']})";
			} else if (!empty($this->params['named']['packageIds'])) {
				$this->page = 1;
	            $this->perPage = 9999;
	            $this->limit = 9999;
				$conditions = "packageId IN({$this->params['named']['packageIds']})";
			}else {
		        $conditions = $this->_build_conditions($this->data);
			}


	        if (!empty($liveDuringStartDate)) {
	            $this->data['condition1'] = $condition1Saved; // restore this so the drop down reflects the right data
	            if (strlen($conditions)) {
	                $conditions .= " AND ";
	            }

	            $conditions .= "(schedulingInstanceStartDate BETWEEN '$liveDuringStartDate' AND '$liveDuringEndDate' + INTERVAL 1 DAY";
	            $conditions .= " OR schedulingInstanceEndDate BETWEEN '$liveDuringStartDate' AND '$liveDuringEndDate' + INTERVAL 1 DAY";
	            $conditions .= " OR (schedulingInstanceStartDate <= '$liveDuringStartDate' AND schedulingInstanceEndDate >= '$liveDuringEndDate')";
	            $conditions .= ")";
	        }

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'schedulingMasterId';

	            $this->set('sortBy', 'schedulingMasterId');
    	        $this->set('sortDirection', 'DESC');
	        }

            if (empty($conditions)) {
                $conditions = '1=1';
            }

            $sql = "CALL imrReport(\"$conditions\", '$order', '{$this->limit}', @numRecords)";

	        $results = $this->OfferType->query($sql);

	        $sql2 = 'SELECT @numRecords as numRecords';

	        $count = $this->OfferType->query($sql2);
    	    $numRecords = $count[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }

	    if (empty($this->data)) {
	        $this->data['condition1']['field'] = 'liveDuring';
            $this->data['condition1']['value']['between'][] = date('Y-m-d', strtotime('2 months ago'));
            $this->data['condition1']['value']['between'][] = date('Y-m-d', strtotime('+1 month'));
        }


	        $country = new Country;
	        $state = new State;
	        $offerType = new OfferType;
	        $this->set('countries', $country->find('list'));
	        $this->set('states', $state->find('list'));
	        $this->set('offerTypeIds', $offerType->find('list'));
	}

	function packages() {
	    if (!empty($this->data)) {
	        $conditions = $this->_build_conditions($this->data);

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;

	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Client.clientId';

	            $this->set('sortBy', 'Client.clientId');
    	        $this->set('sortDirection', 'DESC');
	        }

            if (empty($conditions)) {
                $conditions = '1=1';
            }

            $sql = "SELECT
                        COUNT(cl.clientLoaPackageRelId) as numRecords
                    FROM package AS Package
                        LEFT JOIN packageStatus AS PackageStatus USING (packageStatusId)
                        LEFT JOIN clientLoaPackageRel AS cl USING (packageId)
                        LEFT JOIN client AS Client USING (clientId)
                        INNER JOIN loa AS Loa USING (loaId)
                        LEFT JOIN track AS Track USING (trackId)
                        LEFT JOIN revenueModel as RevenueModel USING (revenueModelId)
                        WHERE Loa.endDate >= NOW() AND $conditions";

	        $count = $this->OfferType->query($sql);
    	    $numRecords = $count[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

            $sql = "SELECT
                        Client.clientId,
                        Client.name,
                        Package.packageId,
                        Package.packageName,
                        PackageStatus.packageStatusName,
                        RevenueModel.revenueModelName,
                        Client.managerUsername
                    FROM package AS Package
                    LEFT JOIN packageStatus AS PackageStatus USING (packageStatusId)
                    LEFT JOIN clientLoaPackageRel AS cl USING (packageId)
                    LEFT JOIN client AS Client USING (clientId)
                    LEFT JOIN loa AS Loa USING (loaId)
                    LEFT JOIN track AS Track USING (trackId)
                    LEFT JOIN revenueModel as RevenueModel USING (revenueModelId)
                    WHERE Loa.endDate >= NOW() AND $conditions
                    GROUP BY cl.clientLoaPackageRelId
                    ORDER BY $order
	                LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);
	        $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
	        $this->set('serializedFormInput', serialize($this->data));
	    }

            $condition1Options = array('MATCH=Client.name' => 'Client Name',
                                        'MATCH=Client.managerUsername' => 'Manager Username');
            $revenueModel = new RevenueModel;
            $packageStatus = new PackageStatus;
            $this->set('condition1Options', $condition1Options);
            $this->set('revenueModelIds', $revenueModel->find('list'));
            $this->set('packageStatusIds', $packageStatus->find('list'));
	}

	function car_033111() {
	    if (!empty($this->data) || !empty($this->params['named']['clientId'])) {
	        $clientId = (!empty($this->params['named']['clientId'])) ? $this->params['named']['clientId'] : $this->data['Client']['clientName_id'];

	        $stats = $this->OfferType->query("SELECT DATE_FORMAT(activityStart, '%Y%m' ) as yearMonth,
	                                                    phone, webRefer, productView, searchView, destinationView, email
	                                            FROM reporting.carConsolidatedView AS rs
	                                            WHERE CURDATE() - INTERVAL 14 MONTH <= activityStart
	                                            AND rs.clientId = '$clientId'
	                                            ORDER BY activityStart ");

	        $auctions = $this->OfferType->query("SELECT DATE_FORMAT(auc.firstTicketDate, '%Y%m' ) as yearMonth, tickets as aucTickets, revenue as aucRevenue
    	                                            FROM reporting.carAuctionSold AS auc
    	                                            WHERE CURDATE() - INTERVAL 14 MONTH <= firstTicketDate
    	                                            AND auc.clientid = '$clientId'
    	                                            ORDER BY firstTicketDate ");

    	    $fixedprice = $this->OfferType->query("SELECT DATE_FORMAT(fp.firstTicketDate, '%Y%m' ) as yearMonth, tickets as fpTickets, revenue as fpRevenue
    	                                            FROM reporting.carFixedPriceSold AS fp
    	                                            WHERE CURDATE() - INTERVAL 14 MONTH <= firstTicketDate
    	                                            AND fp.clientid = '$clientId'
    	                                            ORDER BY firstTicketDate ");

    	    $auctionsTotal = $this->OfferType->query("SELECT DATE_FORMAT(auc.minStartDate, '%Y%m' ) as yearMonth, numberAuctions
        	                                        FROM reporting.carAuction AS auc
        	                                        WHERE CURDATE() - INTERVAL 14 MONTH <= minStartDate
        	                                        AND auc.clientid = '$clientId'
        	                                        ORDER BY minStartDate ");

            $fixedpriceTotal = $this->OfferType->query("SELECT DATE_FORMAT(fp.lastUpdate, '%Y%m' ) as yearMonth, numberPackages
        	                                        FROM reporting.carFixedPricePackage AS fp
        	                                        WHERE CURDATE() - INTERVAL 14 MONTH <= lastUpdate
        	                                        AND fp.clientid = '$clientId'
        	                                        ORDER BY lastUpdate ");

			$hotelOfferTotal = $this->OfferType->query("SELECT DATE_FORMAT(ho.snapShotDate, '%Y%m' ) as yearMonth, numberOffers
        	                                        FROM reporting.carHotelOffer AS ho
        	                                        WHERE CURDATE() - INTERVAL 14 MONTH <= snapShotDate
        	                                        AND ho.clientid = '$clientId'
        	                                        ORDER BY snapShotDate ");

			$hotelOfferClicksTotal = $this->OfferType->query("SELECT year2, month2, event12
			                                                    FROM reporting.carPortfolioReferral AS pr
																WHERE CURDATE() - INTERVAL 14 MONTH <= STR_TO_DATE(CONCAT(year2,'-',month2), '%Y-%m')
																AND pr.clientId = '$clientId'
					                                            ORDER BY year2, month2");

           	foreach ($hotelOfferClicksTotal as $k => $v) {
				$hotelOfferClicksTotal[$k][0]['yearMonth'] = $v['pr']['year2'] . str_pad($v['pr']['month2'], 2, '0', STR_PAD_LEFT);
				unset($hotelOfferClicksTotal[$k]['pr']['month2']);
				unset($hotelOfferClicksTotal[$k]['pr']['year2']);
			}

            //setup array of all months we are using in this view

            $today = strtotime(date('Y-m-01'));
            for ($i = 0; $i <= 12; $i++) {
                $ts = strtotime("-".(12-($i-1))." months", $today);
                $months[$i] = date('Ym', $ts);
                $monthNames[$i] = date('M Y', $ts);
            }

            foreach($auctions as $k => $v):
                $auctionsKeyed[$v[0]['yearMonth']] = $v['auc'];     //set the key for each to the year and month so we can iterate through it easily
            endforeach;

            foreach($fixedprice as $k => $v):
                $fpKeyed[$v[0]['yearMonth']] = $v['fp'];           //set the key for each to the year and month so we can iterate through it easily
            endforeach;

            foreach($auctionsTotal as $k => $v):
                $aucTotKeyed[$v[0]['yearMonth']] = $v['auc'];           //set the key for each to the year and month so we can iterate through it easily
            endforeach;

            foreach($fixedpriceTotal as $k => $v):
                $fpTotKeyed[$v[0]['yearMonth']] = $v['fp'];           //set the key for each to the year and month so we can iterate through it easily
            endforeach;

			foreach($hotelOfferTotal as $k => $v):
                $hotelOfferKeyed[$v[0]['yearMonth']] = $v['ho'];           //set the key for each to the year and month so we can iterate through it easily
            endforeach;

			foreach($hotelOfferClicksTotal as $k => $v):
                $hotelOfferClicksKeyed[$v[0]['yearMonth']] = $v['pr'];           //set the key for each to the year and month so we can iterate through it easily
            endforeach;

            //sum the totals for the last 12 months and put everything in an array we can easily reference later
            foreach($stats as $k => $v):
                $keyedStats[$v[0]['yearMonth']] = array_merge($v['rs'], $v[0]);           //set the key for each to the year and month so we can iterate through it easily
            endforeach;


            $totals['phone'] = 0;
            $totals['webRefer'] = 0;
            $totals['productView'] = 0;
            $totals['searchView'] = 0;
            $totals['destinationView'] = 0;
            $totals['email'] = 0;
            $totals['aucTickets'] = 0;
            $totals['aucRevenue'] = 0;
            $totals['fpTickets'] = 0;
            $totals['fpRevenue'] = 0;
            $totals['aucTotals'] = 0;
            $totals['fpTotals'] = 0;
			$totals['hotelOfferTotal'] = 0;
			$totals['hotelOfferClicksTotal'] = 0;

            foreach ($months as $k => $month):
                $keyedResults[$month] = array_merge((array)@$keyedStats[$month],
                                                (array)@$auctionsKeyed[$month],
                                                (array)@$fpKeyed[$month],
                                                (array)@$aucTotKeyed[$month],
                                                (array)@$fpTotKeyed[$month],
												(array)@$hotelOfferKeyed[$month],
												(array)@$hotelOfferClicksKeyed[$month]
												);

                //only count the last 12 months in the totals, months array has 13 months
                if ($k == 0) {
                    continue;
                }
                $totals['phone'] += @$keyedResults[$month]['phone'];
                $totals['webRefer'] += @$keyedResults[$month]['webRefer'];
                $totals['productView'] += @$keyedResults[$month]['productView'];
                $totals['searchView'] += @$keyedResults[$month]['searchView'];
                $totals['destinationView'] += @$keyedResults[$month]['destinationView'];
                $totals['email'] += @$keyedResults[$month]['email'];
                $totals['aucTickets'] += @$keyedResults[$month]['aucTickets'];
                $totals['aucRevenue'] += @$keyedResults[$month]['aucRevenue'];
                $totals['fpTickets'] += @$keyedResults[$month]['fpTickets'];
                $totals['fpRevenue'] += @$keyedResults[$month]['fpRevenue'];
                $totals['aucTotals'] += @$keyedResults[$month]['numberAuctions'];
                $totals['fpTotals'] += @$keyedResults[$month]['numberPackages'];
				$totals['hotelOfferTotal'] += @$keyedResults[$month]['numberOffers'];
				$totals['hotelOfferClicksTotal'] +=@$keyedResults[$month]['event12'];
            endforeach;

            $results = $keyedResults;

            $client = new Client;
            $client->recursive = -1;
            $clientDetails = $client->read(null, $clientId);

            $client->Loa->recursive = -1;
            $loa = $client->Loa->find('first', array('conditions' => array('Loa.clientId' => $client->id)));

            $clientDetails['Loa'] = $loa['Loa'];

            $this->set(compact('results', 'totals', 'months', 'monthNames', 'clients', 'clientDetails'));
	    }
	}

	function mcr() {

		$this->Client = new Client;
		$startDate = date("Y-m-d 00:00:00");
		$endDate = date("Y-m-d 23:59:59");
		$startDate2 = date("Y-m-d");

		if (!empty($_POST['downloadcsv']) && $_POST['downloadcsv'] == 1) {
			Configure::write('debug', 0);
			$this->set('clients', unserialize(stripslashes( htmlspecialchars_decode($_POST['clients']))));
			$this->viewPath .= '/csv';
	    $this->layoutPath = 'csv';
		} else {

			if (!empty($this->data) || isset($this->params['named']['ql'])) {

				// work around to get report to properly display properties with zero offers
				// in short, properties with zero offers are properly highlighted when displaying
				// full report, but when link is clicked (
				// to display only properties with zero offers today,
				// results are mangled. So instead of working through this slop of code, just display the
				// the full report and set the view to only display those properties with zero offers
				// mbyrnes

				if (isset($this->params['named']['ql'])==2 && $this->params['named']['ql']==2){
					$this->params['named']['ql']='';
					$this->set("zero_offers_only",1);
				}

				$conditions = $this->_build_conditions($this->data);

				if (!empty($this->params['named']['sortBy'])) {

					$direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
					$order = $this->params['named']['sortBy'].' '.$direction;
					$this->set('sortBy', $this->params['named']['sortBy']);
					$this->set('sortDirection', $direction);

				} else {


					switch(@$this->params['named']['ql']):
					case 1:
					$order = "Loa2.endDate";
					break;
					case 2:
					case 3:
					case 5:
					case 6:
					case 7:
					$order = "Loa.membershipBalance DESC";
					break;
					case 4:
					$order = "Loa.endDate";
					break;
					default:
					$order = 'Loa.endDate';
					break;
					endswitch;

					$this->set('sortBy', $order);
					$this->set('sortDirection', 'DESC');
        }

            if (!empty($conditions) || isset($this->params['named']['ql'])) {

			switch(@$this->params['named']['ql']):
			case 1:
				$qlconditions = "NOW() + INTERVAL 90 DAY >= Loa.endDate";
			break;
			case 2:
				$clients = $this->Client->query("CALL clientsNoScheduledOffersLoa('$startDate2','$startDate2')");
				foreach($clients as $c) {
					$clientIds[] = $c['c']['clientId'];
				}

				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			case 3:
				$clients = $this->Client->query("SELECT DISTINCT cl.clientId FROM clientLoaPackageRel cl INNER JOIN package USING(packageId) INNER JOIN loa USING(loaId) WHERE NOW() + INTERVAL 60 DAY >= validityEndDate AND validityEndDate >= NOW() AND loa.inactive <> 1");
				foreach($clients as $c) {
					$clientIds[] = $c['cl']['clientId'];
				}
				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			case 4:
				$noCalls = $this->Client->query("SELECT DISTINCT clientId, MAX(activityStart) as latestDate, activityStart, phone
												FROM reporting.carConsolidatedView as Referrals
												WHERE activityStart >= (NOW() - INTERVAL 120 DAY) AND phone = 0 OR phone IS NULL
												GROUP BY clientId
												HAVING activityStart = latestDate AND latestDate >= NOW() - INTERVAL 60 DAY");

				foreach($noCalls as $c) {
					$clientIds[] = $c['Referrals']['clientId'];
				}
				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			case 5:
				$this->Client->query("CREATE TEMPORARY TABLE recentTickets SELECT clientLoaPackageRel.clientId FROM ticket AS Ticket INNER JOIN clientLoaPackageRel USING(packageId) WHERE Ticket.created >= (NOW() - INTERVAL {$this->params['named']['days']} DAY)");
                $this->Client->query("CREATE INDEX clientId ON recentTickets (clientId)");
				$noSell = $this->Client->query("SELECT DISTINCT Client.clientId
												FROM client AS Client
												INNER JOIN loa AS Loa USING(clientId)
												INNER JOIN clientLoaPackageRel USING(clientId)
												LEFT JOIN recentTickets USING (clientId)
												WHERE recentTickets.clientId IS NULL AND Loa.inactive <> 1 AND Loa.endDate > NOW()");

				foreach($noSell as $c) {
					$clientIds[] = $c['Client']['clientId'];
				}
				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			case 6:
				$this->Client->query("CREATE TEMPORARY TABLE recentTickets SELECT clientLoaPackageRel.clientId FROM ticket AS Ticket INNER JOIN clientLoaPackageRel USING(packageId) WHERE Ticket.created >= (NOW() - INTERVAL 30 DAY) AND Ticket.offerTypeId IN(3,4)");
				$noSell = $this->Client->query("SELECT DISTINCT Client.clientId
												FROM client AS Client
												INNER JOIN loa AS Loa USING(clientId)
												INNER JOIN clientLoaPackageRel USING(clientId)
												LEFT JOIN recentTickets USING (clientId)
												WHERE recentTickets.clientId IS NULL AND Loa.inactive <> 1 AND Loa.endDate > NOW()");

				foreach($noSell as $c) {
					$clientIds[] = $c['Client']['clientId'];
				}
				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			case 7:
				$clients = $this->Client->query("CALL clientsNoScheduledOffersLoaFlex('$startDate2','$startDate2', true)");
				foreach($clients as $c) {
					$clientIds[] = $c['c']['clientId'];
				}

				$qlconditions = "Client.clientId IN (".implode(',', $clientIds).")";
			break;
			default:
				$conditions = $conditions;
			break;
			endswitch;

			if (!empty($qlconditions) && !empty($conditions)) {
				$conditions .= ' AND '.$qlconditions;
			} else if(!empty($qlconditions)) {
				$conditions = $qlconditions;
			}

			$clients = $this->Client->query("SELECT
							Client.clientId,
	                        Client.name,
                            Client.sites,
							Loa.loaId,
							Loa.startDate,
							Loa.endDate,
							Loa2.startDate,
							Loa.loaLevelId,
							Loa.membershipBalance,
							Loa.membershipFee,
							IF(Loa.totalKept = 0 OR Loa.totalKept IS NULL, 'N/A',ROUND(-Loa.membershipBalance / (Loa.totalKept / (DATEDIFF(Loa.startDate,NOW()))))) as daysUntilKeepEnd
	                    FROM loa AS Loa
	                    INNER JOIN client AS Client ON (Client.clientId = Loa.clientId AND curdate() BETWEEN Loa.startDate AND Loa.endDate AND Loa.inactive <> 1)
                        INNER JOIN clientSiteExtended cse ON Client.clientId = cse.clientId
						LEFT JOIN loa AS Loa2 ON (Loa2.clientId = Client.clientId AND Loa2.startDate > NOW() AND Loa2.inactive <> 1)
						LEFT JOIN clientDestinationRel AS ClientDestinationRel ON(Client.clientId = ClientDestinationRel.clientId)
	                    WHERE $conditions AND cse.inactive <> 1 AND Loa.inactive <> 1
	                    GROUP BY Loa.loaId
	                    ORDER BY $order");
			}
		}

	    if (isset($this->data['MCR'])) {
			switch($this->data['MCR']['pkgRevenueRange']):
				case 1:
					$pkgRevenueStart = date('Y-m-d', strtotime('-60 days'));
					break;
				case 2:
					$pkgRevenueStart = date('Y-m-d', strtotime('-90 days'));
					break;
				case 3:
					$pkgRevenueStart = date('Y-m-01');
					break;
				case 4:
					if (date('Y-m-d') < date('Y-04-01')) {
						$pkgRevenueStart = date('Y-01-01');
					} else if (date('Y-m-d') < date('Y-07-01')) {
						$pkgRevenueStart = date('Y-04-01');
					} else if (date('Y-m-d') < date('Y-10-01')) {
						$pkgRevenueStart = date('Y-07-01');
					} else {
						$pkgRevenueStart = date('Y-10-01');
					}
					break;
				case 0:
				default:
					$pkgRevenueStart = date('Y-m-d', strtotime('-30 days'));
				break;
			endswitch;
		}
		else {
			$pkgRevenueStart = date('Y-m-d', strtotime('-30 days'));
		}

		$pkgRevenueEnd = date('Y-m-d', strtotime("+1 day"));
		if (!isset($clients)) {
			 $clients = array();
		}

		$referrals = $this->Client->query("SELECT DATE_FORMAT(MAX(activityStart), '%Y-%m-%d') as latestReferralDate
										FROM reporting.carConsolidatedView as Referrals");
		$latestReferralDate = $referrals[0][0]['latestReferralDate'];

		foreach($clients as $k => $client) {
			$clientId = $client['Client']['clientId'];

			//sites
			//$clients[$k]['Client']['sites'] = $this->Client->get_sites($clientId);

			if (isset($this->data['MCR'])) {
				  if ($this->data['MCR']['pkgRevenueRange'] == 5) {
				  	$pkgRevenueStart = date('Y-m-d', strtotime($client['Loa']['startDate']));
				}
			}

			#### Package Revenue ####
			// Begin Packages Live Today
			$packagesLive = $this->Client->query("SELECT COUNT(DISTINCT SchedulingMaster.packageId) as packagesLive, offerTypeId, siteId FROM schedulingInstance as SchedulingInstance
												INNER JOIN schedulingMaster as SchedulingMaster USING (schedulingMasterId)
												INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel USING(packageId)
												WHERE (SchedulingInstance.startDate between '$startDate' and '$endDate' or SchedulingInstance.endDate between '$startDate' and '$endDate' or (SchedulingInstance.startDate < '$startDate' and SchedulingInstance.endDate > '$endDate'))
														AND clientId = $clientId
												GROUP BY offerTypeId, siteId");

			if (true) {
				$packagesLiveFlex = $this->Client->query("SELECT COUNT(DISTINCT SchedulingMaster.packageId) as packagesLive, offerTypeId, SchedulingMaster.siteId
													FROM schedulingInstance as SchedulingInstance
													INNER JOIN schedulingMaster as SchedulingMaster USING (schedulingMasterId)
													INNER JOIN package USING (packageId)
													INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel USING(packageId)
													WHERE (SchedulingInstance.startDate between '$startDate' and '$endDate' or SchedulingInstance.endDate between '$startDate' and '$endDate' or (SchedulingInstance.startDate < '$startDate' and SchedulingInstance.endDate > '$endDate'))
													AND clientId = $clientId
													AND package.isFlexPackage = 1
													GROUP BY offerTypeId, SchedulingMaster.siteId");
			}

			$packageStats = array();
			$packageStats['packagesLiveTodayLL'] = $packageStats['packagesLiveTodayFG'] = $packageStats['auctionsLiveToday'] = $packageStats['fpLiveToday'] = 0;

			foreach ($packagesLive as $v) {
                switch($v['SchedulingMaster']['siteId']) {
                    case 1:         //LL
                        $packageStats['packagesLiveTodayLL'] += $v[0]['packagesLive'];
                        break;
                    case 2:         //FG
                        $packageStats['packagesLiveTodayFG'] += $v[0]['packagesLive'];
                        break;
                    default:
                        break;
                }
				switch($v['SchedulingMaster']['offerTypeId']) {
					case 1:
					case 2:
					case 6:
						$packageStats['auctionsLiveToday'] += $v[0]['packagesLive'];
					break;
					case 3:
					case 4:
						$packageStats['fpLiveToday'] += $v[0]['packagesLive'];
				}
			}

            // if (@$this->params['named']['ql'] == 7) {
			if (true) {
                $packageStats['packagesLiveTodayLLFlex'] = $packageStats['packagesLiveTodayFGFlex'] = $packageStats['auctionsLiveTodayFlex'] = $packageStats['fpLiveTodayFlex'] = 0;
				foreach ($packagesLiveFlex as $v) {
					switch($v['SchedulingMaster']['siteId']) {
						case 1:         //LL
							$packageStats['packagesLiveTodayLLFlex'] += $v[0]['packagesLive'];
							break;
						case 2:         //FG
							$packageStats['packagesLiveTodayFGFlex'] += $v[0]['packagesLive'];
							break;
						default:
							break;
					}
					switch($v['SchedulingMaster']['offerTypeId']) {
						case 1:
						case 2:
						case 6:
							$packageStats['auctionsLiveTodayFlex'] += $v[0]['packagesLive'];
						break;
						case 3:
						case 4:
							$packageStats['fpLiveTodayFlex'] += $v[0]['packagesLive'];
					}
				}
			}


			// End Packages Live Today
			// Packages Uptime
			$packageUptime = $this->Client->query("CALL clientPackagesUptime($clientId, '$pkgRevenueStart', '$pkgRevenueEnd' )");
			$packageStats['packageUptime'] = $packageUptime[0][0]['packageUptime'];
			// End Packages Uptime

			// Begin Total Sold/Total $$
			$tickets = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId) as totalTickets,
											SUM(Ticket.billingPrice) AS totalRevenue,
											ROUND(COUNT(DISTINCT Ticket.ticketId) / (SELECT COUNT(DISTINCT OfferLive.offerId) FROM offerLuxuryLink AS OfferLive INNER JOIN clientLoaPackageRel as cl2 USING(packageId) WHERE cl2.clientId = $clientId  AND OfferLive.offerTypeId = Ticket.offerTypeId AND (OfferLive.startDate between '$pkgRevenueStart' and '$pkgRevenueEnd' or OfferLive.endDate between '$pkgRevenueStart' and '$pkgRevenueEnd' or (OfferLive.startDate < '$pkgRevenueStart' and OfferLive.endDate > '$pkgRevenueEnd'))) * 100) as closeRate,
											Ticket.offerTypeId
											FROM ticket as Ticket
											INNER JOIN package as Package USING (packageId)
											INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel USING(packageId)
											WHERE Ticket.created between '$pkgRevenueStart' and '$pkgRevenueEnd'
												AND ClientLoaPackageRel.clientId = $clientId
											GROUP BY Ticket.offerTypeId");

			$totalRemitted = $this->Client->query("SELECT SUM(Ticket.billingPrice) AS totalLoaRevenue
											FROM ticket as Ticket
											INNER JOIN package as Package USING (packageId)
											INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel USING(packageId)
											WHERE Ticket.created >= '{$client['Loa']['startDate']}' AND ClientLoaPackageRel.loaId = {$client['Loa']['loaId']}
												AND ClientLoaPackageRel.clientId = $clientId");

			$totalRemitted['totalLoaRemitted'] = $totalRemitted[0][0]['totalLoaRevenue'] - $client['Loa']['membershipFee'];

			$ticketStats = array();
			$ticketStats['totalSold'] = $ticketStats['totalRevenue'] = $ticketStats['fpRequests'] = $ticketStats['auctionCloseRate'] = 0;

			foreach ($tickets as $v) {
				$ticketStats['totalSold'] += $v[0]['totalTickets'];
				$ticketStats['totalRevenue'] += $v[0]['totalRevenue'];

				switch($v['Ticket']['offerTypeId']) {
					case 1:
					case 2:
					case 6:
						$ticketStats['auctionCloseRate'] += $v[0]['closeRate'];
					break;
					case 3:
					case 4:
						$ticketStats['fpRequests'] += $v[0]['totalTickets'];
				}
			}
			// End Total Sold/Total $$
			### End Packages Revenue
			// Begin Referrals/Impressions
			$referrals = $this->Client->query("SELECT activityStart, webRefer, phone, productView, searchView, email, destinationView
											FROM reporting.carConsolidatedView as Referrals
											WHERE Referrals.clientId = $clientId AND activityStart = '$latestReferralDate'");
			// End Referrals/Impressions
			$clients[$k] = @array_merge($clients[$k], (array)$packageStats, (array)$ticketStats, (array)$referrals[0], $totalRemitted);
		}

		//Setup Client Manager Usernames
		$tmp = $this->Client->query("SELECT DISTINCT LOWER(managerUsername) as username FROM client INNER JOIN adminUser ON(adminUser.username = client.managerUsername)");

		foreach ($tmp as $username) {
			$managerUsernames[$username[0]['username']] = $username[0]['username'];
		}

		$this->set('managerUsernames', $managerUsernames);
		//

		//Setup Regions
		$tmp = $this->Client->query("SELECT destinationId, parentId, destinationName FROM destination as Destination ORDER BY destinationName");

		foreach ($tmp as $k => $v) {
			$regions[$v['Destination']['destinationId']] = $v['Destination']['destinationName'];
		}
		//$regions = $this->destinationTree($tmp);

		$this->set('regions', $regions);

		$pkgRevenueRanges = array('Past 30 Days',
									'Past 60 Days',
									'Past 90 Days',
									'Month to Date',
									'Quarter to Date',
									'Since LOA Start Date');

		$this->set('pkgRevenueRanges', $pkgRevenueRanges);
		$this->set('latestReferralDate', $latestReferralDate);

		//all of the client data aggregated in this one array
		$this->set('clients', $clients);
		}
	}

	function destinationTree($list) {
		$lookup = array();
		foreach( $list as $item )
		{
		    $item['children'] = array();
		    $lookup[$item['Destination']['destinationId']] = $item['Destination'];
		}

		$tree = array();
		foreach( $lookup as $id => $foo )
		{
		    $item = &$lookup[$id];
		    if( $item['parentId'] == 0 )
		    {
		        $tree[$id] = &$item;
		    }
		    else
		    if( isset( $lookup[$item['parentId']] ) )
		    {
		        $lookup[$item['parentId']]['children'][$id] = &$item;
		    }
		    else
		    {
		        $tree['_orphans_'][$id] = &$item;
		    }
		}

		$this->reduceDestinationTree($tree);

		return $tree;
	}

	function reduceDestinationTree(&$a, $depth)
	{
	    foreach($a as $key=>$value)
	    {
			//debug($a);
	        if (is_array($value))
	           $this->reduceDestinationTree($a[$key], $depth+1);

	    }

	}


	// Will return the number of days between the two dates passed in
	function count_days( $a, $b )
	{
	    // First we need to break these dates into their constituent parts:
	    $gd_a = getdate( $a );
	    $gd_b = getdate( $b );

	    // Now recreate these timestamps, based upon noon on each day
	    // The specific time doesn't matter but it must be the same each day
	    $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	    $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );

	    // Subtract these two numbers and divide by the number of seconds in a
	    //  day. Round the result since crossing over a daylight savings time
	    //  barrier will cause this time to be off by an hour or two.
	    return round( abs( $a_new - $b_new ) / 86400 );
	}

	function merch_031811() {
		$this->Client = new Client();

		if(!empty($this->data['datePicker'])){
			$date = $this->data['datePicker'];
		} else {
			$date = date('Y-m-d');
		}
		$this->set('date', $date);
		$dateTs = strtotime($date);
		$lastMonth = date('Y-m-01', strtotime('-1 month', $dateTs));
		$twoMonthsAgo = date('Y-m-01', strtotime('-2 month', $dateTs));
		$lastMonthLastYear = date('Y-m-01', strtotime('-13 months', $dateTs));

		$lastMonthEnd = date('Y-m-t', strtotime('-1 month', $dateTs));
		$twoMonthsAgoEnd = date('Y-m-t', strtotime('-2 month', $dateTs));
		$lastMonthLastYearEnd = date('Y-m-t', strtotime('-13 months', $dateTs));

		$this->set(compact('lastMonthDisplay', 'twoMonthsAgoDisplay', 'lastMonthLastYearDisplay'));



		$auctions = array(1,2,6);

		if (isset($this->data['site']) && $this->params['data']['site'] == 'family') {
			$siteId = 2;
			$siteName = 'Family';
		} else {
			$siteId = 1;
			$siteName = 'LuxuryLink';
		}

		$siteCondition = "schedulingMaster.siteId = $siteId";
        $loaSiteCondition = "Loa.sites LIKE '%{$siteName}%'";
		//$loaSiteCondition = "EXISTS(SELECT * FROM multiSite WHERE model = 'Loa' and modelId = Loa.loaId and sites LIKE '%$siteName%')";
		$offerLive = "offer$siteName";
		$ticketSiteCondition = "Ticket.siteId = $siteId";

		# AUCTIONS/FP CLOSING
		/* Auctions/Fp Closing Today/Yesterday */
		$tmp = $this->Client->query("SELECT COUNT(*) as numClosing, DATE_FORMAT(schedulingInstance.endDate, '%Y-%m-%d') as theDate FROM schedulingInstance
											INNER JOIN schedulingMaster USING(schedulingMasterId)

											WHERE DATE_FORMAT(schedulingInstance.endDate, '%Y-%m-%d') BETWEEN '$date' - INTERVAL 1 DAY AND '$date'
												AND offerTypeId IN (1,2,6) AND $siteCondition
											GROUP BY DATE_FORMAT(schedulingInstance.endDate, '%Y-%m-%d');");

		foreach ($tmp as $v) {
			if ($v[0]['theDate'] == $date) {
				$sales[1][1] = $v[0]['numClosing'];
			} else {
				$sales[1][2] = $v[0]['numClosing'];
			}
		}

		/* Auctions/Fp Closing Last 7 Daily Average */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(*)/7) as dailyAverage FROM schedulingInstance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											WHERE schedulingInstance.endDate BETWEEN '$date' - INTERVAL 6 DAY AND '$date' + INTERVAL 1 DAY  AND $siteCondition
											AND offerTypeId IN (1,2,6)");
		$sales[1][3] = $tmp[0][0]['dailyAverage'];

		/* Auctions/Fp Closing Last 30 Daily Average */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(*)/30) as dailyAverage FROM schedulingInstance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											WHERE schedulingInstance.endDate BETWEEN '$date' - INTERVAL 29 DAY AND '$date' + INTERVAL 1 DAY   AND $siteCondition
											AND offerTypeId IN (1,2,6)");
		$sales[1][4] = $tmp[0][0]['dailyAverage'];

		/* Auctions/Fp Closing Last 90 Daily Average */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(*)/90) as dailyAverage FROM schedulingInstance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											WHERE schedulingInstance.endDate BETWEEN '$date' - INTERVAL 89 DAY AND '$date' + INTERVAL 1 DAY  AND $siteCondition
											AND offerTypeId IN (1,2,6)");
		$sales[1][5] = $tmp[0][0]['dailyAverage'];

		/* Auctions/Fp Closing Last 365 Daily Average */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(*)/365) as dailyAverage FROM schedulingInstance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											WHERE schedulingInstance.endDate BETWEEN '$date' - INTERVAL 364 DAY AND '$date' + INTERVAL 1 DAY  AND $siteCondition
											AND offerTypeId IN (1,2,6)");
		$sales[1][6] = $tmp[0][0]['dailyAverage'];


		# END AUCTIONS CLOSING

		# AUCTIONS/FP CLOSING WITH FUNDED TICKETS
		/* Today/Yesterday */

		// [ALEE] RECODED THIS SECTION JAN 22 2010 -- CHANGED TO AUCTIONS CLOSING WITH FUNDED TICKETS
		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.offerId) AS numClosing, DATE_FORMAT(Ticket.created, '%Y-%m-%d') as theDate FROM ticket AS Ticket
											WHERE Ticket.formatId = 1 AND Ticket.created BETWEEN '$date' - INTERVAL 1 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition AND Ticket.ticketStatusId IN (3,4,5,6)
											GROUP BY DATE_FORMAT(Ticket.created, '%Y-%m-%d')");

		foreach ($tmp as $v) {
			if ($v[0]['theDate'] == $date) {
				$sales[2][1] = $v[0]['numClosing'];
				$sales[3][1] = ROUND($sales[2][1]/$sales[1][1]*100,1);
			} else {
				$sales[2][2] = $v[0]['numClosing'];
				$sales[3][2] = ROUND($sales[2][2]/$sales[1][2]*100,1);
			}
		}

		/* Last 7 Days Avg */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(DISTINCT Ticket.offerId)/7) as dailyAverage FROM ticket AS Ticket
											WHERE Ticket.formatId = 1 AND Ticket.created BETWEEN '$date' - INTERVAL 6 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition AND Ticket.ticketStatusId IN (3,4,5,6)");
		$sales[2][3] = $tmp[0][0]['dailyAverage'];
		$sales[3][3] = ROUND($sales[2][3]/$sales[1][3]*100,1);

		/* Last 30 Days Avg */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(DISTINCT Ticket.offerId)/30) as dailyAverage FROM ticket AS Ticket
											WHERE Ticket.formatId = 1 AND Ticket.created BETWEEN '$date' - INTERVAL 29 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition AND Ticket.ticketStatusId IN (3,4,5,6)");
		$sales[2][4] = $tmp[0][0]['dailyAverage'];
		$sales[3][4] = ROUND($sales[2][4]/$sales[1][4]*100,1);

		/* Last 90 Days Avg */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(DISTINCT Ticket.offerId)/90) as dailyAverage FROM ticket AS Ticket
											WHERE Ticket.formatId = 1 AND Ticket.created BETWEEN '$date' - INTERVAL 89 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition AND Ticket.ticketStatusId IN (3,4,5,6)");
		$sales[2][5] = $tmp[0][0]['dailyAverage'];
		$sales[3][5] = ROUND($sales[2][5]/$sales[1][5]*100,1);

		/* Last 365 Days Avg */
		$tmp = $this->Client->query("SELECT ROUND(COUNT(DISTINCT Ticket.offerId)/365) as dailyAverage FROM ticket AS Ticket
											WHERE Ticket.formatId = 1 AND Ticket.created BETWEEN '$date' - INTERVAL 364 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition AND Ticket.ticketStatusId IN (3,4,5,6)");
		$sales[2][6] = $tmp[0][0]['dailyAverage'];
		$sales[3][6] = ROUND($sales[2][6]/$sales[1][6]*100,1);

		# END AUCTIONS CLOSING WITH FUNDED TICKETS

		# AVG SELL PRICES
		/* Today/Yesterday */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(Ticket.billingPrice) as avgSalePrice, offerTypeId, DATE_FORMAT(Ticket.created, '%Y-%m-%d') as theDate, Ticket.ticketStatusId FROM ticket AS Ticket
											WHERE Ticket.created BETWEEN '$date' - INTERVAL 1 DAY AND '$date' + INTERVAL 1 DAY AND Ticket.ticketStatusId IN(3,4,5,6)  AND $ticketSiteCondition
											GROUP BY DATE_FORMAT(Ticket.created, '%Y-%m-%d'), offerTypeId, ticketStatusId");
		$sales[8][1] = 0;
		$sales[8][2] = 0;

		foreach($tmp as $v) {


			if ($v[0]['theDate'] == $date) {
				$col = '1';
			} else {
				$col = '2';
			}

			if (in_array($v['Ticket']['offerTypeId'], $auctions)) {
				$row = '4';
			} else {
				if ($v['Ticket']['ticketStatusId'] == 3) {
					continue;
				}
				$row = '7';
			}

			$sales[$row][$col] = array_merge((array)@$sales[$row][$col], (array)explode(",", $v[0]['avgSalePrice']));

		}

		/* Last 7 Days Avg */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(Ticket.billingPrice) as avgSalePrice, offerTypeId, Ticket.ticketStatusId FROM ticket AS Ticket
											WHERE Ticket.created BETWEEN '$date' - INTERVAL 6 DAY AND '$date' + INTERVAL 1 DAY  AND Ticket.ticketStatusId IN(3,4,5,6)  AND $ticketSiteCondition
											GROUP BY offerTypeId, ticketStatusId");
		$sales[8][3] = 0;
		foreach($tmp as $v) {
			if (in_array($v['Ticket']['offerTypeId'], $auctions)) {
				$row = '4';
			} else {
				if ($v['Ticket']['ticketStatusId'] == 3) {
					continue;
				}
				$row = '7';
			}
			$sales[$row][3] = array_merge((array)@$sales[$row][3], (array)explode(",", $v[0]['avgSalePrice']));
		}

		/* Last 30 Days Avg */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(Ticket.billingPrice) as avgSalePrice, offerTypeId, Ticket.ticketStatusId FROM ticket AS Ticket
											WHERE Ticket.created BETWEEN '$date' - INTERVAL 29 DAY AND '$date' + INTERVAL 1 DAY  AND Ticket.ticketStatusId IN(3,4,5,6) AND $ticketSiteCondition
											GROUP BY offerTypeId, ticketStatusId");
		$sales[8][4] = 0;
		foreach($tmp as $v) {

			if (in_array($v['Ticket']['offerTypeId'], $auctions)) {
				$row = '4';
			} else {
				if ($v['Ticket']['ticketStatusId'] == 3) {
					continue;
				}
				$row = '7';
			}

			$sales[$row][4] = array_merge((array)@$sales[$row][4], (array)explode(",", $v[0]['avgSalePrice']));

		}

		/* Last 90 Days Avg */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(Ticket.billingPrice) as avgSalePrice, offerTypeId, Ticket.ticketStatusId FROM ticket AS Ticket
											WHERE Ticket.created BETWEEN '$date' - INTERVAL 89 DAY AND '$date' + INTERVAL 1 DAY  AND Ticket.ticketStatusId IN(3,4,5,6) AND $ticketSiteCondition
											GROUP BY offerTypeId, ticketStatusId");
		$sales[8][5] = 0;
		foreach($tmp as $v) {

			if (in_array($v['Ticket']['offerTypeId'], $auctions)) {
				$row = '4';
			} else {
				if ($v['Ticket']['ticketStatusId'] == 3) {
					continue;
				}
				$row = '7';
			}

			$sales[$row][5] = array_merge((array)@$sales[$row][5], (array)explode(",", $v[0]['avgSalePrice']));

		}

		/* Last 365 Days Avg */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(Ticket.billingPrice) as avgSalePrice, offerTypeId, Ticket.ticketStatusId FROM ticket AS Ticket
											WHERE Ticket.created BETWEEN '$date' - INTERVAL 364 DAY AND '$date' + INTERVAL 1 DAY AND Ticket.ticketStatusId IN(3,4,5,6) AND $ticketSiteCondition
											GROUP BY offerTypeId, ticketStatusId");
		$sales[8][6] = 0;
		foreach($tmp as $v) {

			if (in_array($v['Ticket']['offerTypeId'], $auctions)) {
				$row = '4';
			} else {
				if ($v['Ticket']['ticketStatusId'] == 3) {
					continue;
				}
				$row = '7';
			}
			$sales[$row][6] = array_merge((array)@$sales[$row][6], (array)explode(",", $v[0]['avgSalePrice']));

		}

		foreach ($sales as $k => $v) {
			if ($k !== 4 && $k !== 7) {
				continue;
			}

			foreach ($v as $k2 => $v2) {
				$sum = array_sum($v2);
				$num = count($v2);
				$sales[$k][$k2] = $sum/$num;
			}
		}
		# END AVG SELL PRICES

		# FP Requests and FP Funded
		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId) as fpRequests, SUM(IF(PaymentDetail.paymentDetailId IS NULL, 0, 1)) as fpFunded,
											DATE_FORMAT(Ticket.created, '%Y-%m-%d') as theDate
											FROM ticket AS Ticket
											LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge = 1)
											WHERE offerTypeId IN (3, 4) AND Ticket.created BETWEEN '$date' - INTERVAL 1 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition
											GROUP BY offerTypeId, DATE_FORMAT(Ticket.created, '%Y-%m-%d')");

		foreach($tmp as $v) {
			if ($v[0]['theDate'] == $date) {
				$col = '1';
			} else {
				$col = '2';
			}

			$sales[5][$col] = $v[0]['fpRequests'];
			$sales[6][$col] = $v[0]['fpFunded'];
		}

		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId)/7 as fpRequests, SUM(IF(PaymentDetail.paymentDetailId IS NULL, 0, 1))/7 as fpFunded FROM ticket AS Ticket
												LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge = 1)
												WHERE offerTypeId IN (3, 4) AND Ticket.created BETWEEN '$date' - INTERVAL 6 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition");
		$sales[5][3] = ROUND($tmp[0][0]['fpRequests']);
		$sales[6][3] = ROUND($tmp[0][0]['fpFunded']);

		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId)/30 as fpRequests, SUM(IF(PaymentDetail.paymentDetailId IS NULL, 0, 1))/30 as fpFunded FROM ticket AS Ticket
												LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge = 1)
												WHERE offerTypeId IN (3, 4) AND Ticket.created BETWEEN '$date' - INTERVAL 29 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition");
		$sales[5][4] = ROUND($tmp[0][0]['fpRequests']);
		$sales[6][4] = ROUND($tmp[0][0]['fpFunded']);

		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId)/90 as fpRequests, SUM(IF(PaymentDetail.paymentDetailId IS NULL, 0, 1))/90 as fpFunded FROM ticket AS Ticket
												LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge = 1)
												WHERE offerTypeId IN (3, 4) AND Ticket.created BETWEEN '$date' - INTERVAL 89 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition");
		$sales[5][5] = ROUND($tmp[0][0]['fpRequests']);
		$sales[6][5] = ROUND($tmp[0][0]['fpFunded']);


		$tmp = $this->Client->query("SELECT COUNT(DISTINCT Ticket.ticketId)/365 as fpRequests, SUM(IF(PaymentDetail.paymentDetailId IS NULL, 0, 1))/365 as fpFunded FROM ticket AS Ticket
												LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.isSuccessfulCharge = 1)
												WHERE offerTypeId IN (3, 4) AND Ticket.created BETWEEN '$date' - INTERVAL 364 DAY AND '$date' + INTERVAL 1 DAY AND $ticketSiteCondition");
		$sales[5][6] = ROUND($tmp[0][0]['fpRequests']);
		$sales[6][6] = ROUND($tmp[0][0]['fpFunded']);


		# END FP Requests and FP Funded

		# TRAVEL REVENUE [ALEE] CHANGED HOW THIS IS CALCULATED REQUEST BY MCHOE JAN 22-2010

		$sales[8][1] = ($sales[2][1] * $sales[4][1]) + ($sales[6][1] * $sales[7][1]);
		$sales[8][2] = ($sales[2][2] * $sales[4][2]) + ($sales[6][2] * $sales[7][2]);
		$sales[8][3] = ($sales[2][3] * $sales[4][3]) + ($sales[6][3] * $sales[7][3]);
		$sales[8][4] = ($sales[2][4] * $sales[4][4]) + ($sales[6][4] * $sales[7][4]);
		$sales[8][5] = ($sales[2][5] * $sales[4][5]) + ($sales[6][5] * $sales[7][5]);
		$sales[8][6] = ($sales[2][6] * $sales[4][6]) + ($sales[6][6] * $sales[7][6]);

		# END TRAVEL REVENUE

		$this->set('sales', $sales);

		#### END SALES ####


		#### Revenue ####
		$mtdStart = date('Y-m-01', strtotime($date));
		if (date('Y-m-d', strtotime($date)) < date('Y-04-01', strtotime($date))) {
			$qtdStart = date('Y-01-01', strtotime($date));
		} else if (date('Y-m-d', strtotime($date)) < date('Y-07-01', strtotime($date))) {
			$qtdStart = date('Y-04-01', strtotime($date));
		} else if (date('Y-m-d', strtotime($date)) < date('Y-10-01', strtotime($date))) {
			$qtdStart = date('Y-07-01', strtotime($date));
		} else {
			$qtdStart = date('Y-10-01', strtotime($date));
		}
		$ytdStart = date('Y-01-01', strtotime($date));

		$revenueMtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket INNER JOIN paymentDetail pd ON(pd.ticketId = Ticket.ticketId AND pd.isSuccessfulCharge = 1) WHERE Ticket.created >= '$mtdStart' AND Ticket.created <= '$date' AND Ticket.ticketStatusId != 8 AND $ticketSiteCondition");
		$revenueQtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket INNER JOIN paymentDetail pd ON(pd.ticketId = Ticket.ticketId AND pd.isSuccessfulCharge = 1) WHERE Ticket.created >= '$qtdStart' AND Ticket.created <= '$date' AND Ticket.ticketStatusId != 8 AND $ticketSiteCondition");
		$revenueYtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket INNER JOIN paymentDetail pd ON(pd.ticketId = Ticket.ticketId AND pd.isSuccessfulCharge = 1) WHERE Ticket.created >= '$ytdStart' AND Ticket.created <= '$date' AND Ticket.ticketStatusId != 8 AND $ticketSiteCondition");
		$this->set(compact('revenueMtd', 'revenueQtd', 'revenueYtd'));
		#### End Revenue ####
	#### BEGIN AGING ####
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT clientId) as clients, COUNT(DISTINCT clientId) as numClients,
										IF(DATEDIFF(NOW(), startDate) > 121, 3, IF(DATEDIFF(NOW(), startDate) > 91, 2, 1)) as severity
										FROM loa AS Loa
										WHERE Loa.startDate < NOW() - INTERVAL 60 DAY
												AND Loa.inactive <> 1 AND Loa.endDate >= NOW()
												AND Loa.membershipBalance > 0 AND $loaSiteCondition
										GROUP BY severity");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT clientId) as numClients,
										IF(DATEDIFF(NOW(), startDate) > 121, 3, IF(DATEDIFF(NOW(), startDate) > 91, 2, 1)) as severity
										FROM loa AS Loa
										WHERE Loa.startDate < NOW() - INTERVAL 60 DAY
												AND Loa.inactive <> 1 AND Loa.endDate >= NOW() AND $loaSiteCondition
										GROUP BY severity");

		foreach ($tmp as $v) {
			$aging[$v[0]['severity']] = @$v[0];
		}
		foreach ($tmp2 as $v) {
			@$aging[$v[0]['severity']]['totalClients'] += @$v[0]['numClients'];
		}

		$this->set('aging', $aging);

	#### END AGING ####


	#### Inventory Management ####
		/* Distressed Auctions */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT schedulingMaster.schedulingMasterId) as ids,
								COUNT(DISTINCT schedulingMaster.schedulingMasterId) as numOffers,
								IF(numOffersNoBid >10, 2, 1) as severity,
								expirationCriteriaId FROM schedulingMasterPerformance
								INNER JOIN schedulingMaster USING(schedulingMasterId)
								INNER JOIN schedulingInstance ON(schedulingInstance.schedulingMasterId = schedulingMaster.schedulingMasterId AND schedulingInstance.endDate >= NOW())
								LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingMaster.schedulingMasterId)
								LEFT JOIN track USING(trackId)
								WHERE numOffersNoBid >= 5 AND $siteCondition
								GROUP BY severity, expirationCriteriaId");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT schedulingMaster.schedulingMasterId) as totalNumOffers,
											expirationCriteriaId FROM schedulingMasterPerformance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											INNER JOIN schedulingInstance ON(schedulingInstance.schedulingMasterId = schedulingMaster.schedulingMasterId AND schedulingInstance.endDate >= NOW())
											LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingMaster.schedulingMasterId)
											LEFT JOIN track USING(trackId)
											WHERE $siteCondition
											GROUP BY expirationCriteriaId");

		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];

			@$distressedAuctions[$row][$col]['numOffers'] += $v[0]['numOffers'];
			@$distressedAuctions[$row][$col]['ids'][] = $v[0]['ids'];
		}

		foreach ($tmp2 as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			@$distressedAuctions[$row]['totalNumOffers'] += $v[0]['totalNumOffers'];
		}

		$this->set('distressedAuctions', $distressedAuctions);

		/* Distressed Buy Nows */
		$tmp = $this->Client->query("SELECT
								GROUP_CONCAT(DISTINCT schedulingInstance.schedulingMasterId) as ids,
								COUNT(DISTINCT OfferLive.offerId) numOffers,
								IF(
									DATEDIFF('$date', Ticket.created) >= 43,
									2,
										IF(DATEDIFF('$date', Ticket.created) >= 21,
										1,
										0)
									) as severity,
								expirationCriteriaId
								FROM $offerLive AS OfferLive
								INNER JOIN offer USING(offerId)
								INNER JOIN schedulingInstance USING(schedulingInstanceId)
								LEFT JOIN ticket AS Ticket USING(offerId)
								LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingInstance.schedulingMasterId)
								LEFT JOIN track USING(trackId)
								WHERE OfferLive.offerTypeId IN(3,4) AND OfferLive.endDate >= NOW()
								GROUP BY severity, expirationCriteriaId");

		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];

			@$distressedBuyNows[$row][$col]['numOffers'] += $v[0]['numOffers'];
			@$distressedBuyNows[$row][$col]['ids'][] = $v[0]['ids'];
		}

		$this->set('distressedBuyNows', $distressedBuyNows);

		/* Packages with x days Validity Left */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT Package.packageId) AS ids, COUNT(*) AS numPackages, IF(DATEDIFF(pricePoint.validityEnd, '$date') < 30, 3, IF(DATEDIFF(pricePoint.validityEnd, '$date') < 45, 2, 1)) AS severity, expirationCriteriaId FROM package AS Package INNER JOIN pricePoint USING (packageId) INNER JOIN clientLoaPackageRel USING(packageId) INNER JOIN loa AS Loa USING(loaId) INNER JOIN track USING(trackId) WHERE (pricePoint.validityStart <= '$date' + INTERVAL 60 DAY) AND pricePoint.validityEnd >= NOW() AND $loaSiteCondition GROUP BY severity, expirationCriteriaId");

		foreach ($tmp as $v) {
			
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];

			@$expiringPackages[$row][$col]['numPackages'] += $v[0]['numPackages'];
			@$expiringPackages[$row][$col]['ids'][] = $v[0]['ids'];
		}

		$this->set('expiringPackages', $expiringPackages);

		/* Auctions w/o buy now */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT Package.packageId) AS ids, COUNT(DISTINCT Package.packageId) AS numPackages,
		 								expirationCriteriaId
										FROM package AS Package
										INNER JOIN $offerLive AS OfferLive2 ON(OfferLive2.packageId = Package.packageId AND OfferLive2.isClosed <> 1 AND OfferLive2.offerTypeId IN(1,2,6))
										LEFT JOIN $offerLive AS OfferLive ON(OfferLive.packageId = Package.packageId AND OfferLive.isClosed <> 1 AND OfferLive.offerTypeId IN(3,4))
										INNER JOIN clientLoaPackageRel cl ON(cl.packageid = Package.packageId)
										INNER JOIN loa AS Loa USING(loaId)
										INNER JOIN track USING(trackId)
										WHERE Package.endDate >= '$date' AND OfferLive.offerId IS NULL
										GROUP BY expirationCriteriaId
										");
		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			@$noBuyNows[$row][1]['numPackages'] += $v[0]['numPackages'];
			@$noBuyNows[$row][1]['ids'][] = $v[0]['ids'];
		}

		$this->set('noBuyNows', $noBuyNows);

		/* Clients with No Packages */
		//get all clients without packages
		$tmp = $this->Client->query("CALL clientsNoScheduledOffersLOA('$date','$date')");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT Loa.clientId) as totalClients FROM loa AS Loa WHERE Loa.inactive <> 1
									 		AND Loa.endDate >= '$date' AND $loaSiteCondition");

		//get the date of the last live package
		$clientsNoPackages[1][1]['numClients'] = 0;
		$clientsNoPackages[1][2]['numClients'] = 0;
		$clientsNoPackages[1][3]['numClients'] = 0;
		$clientsNoPackages[1][4]['numClients'] = 0;
		$clientsNoPackages[2][1]['numClients'] = 0;
		$clientsNoPackages[2][2]['numClients'] = 0;
		$clientsNoPackages[2][3]['numClients'] = 0;
		$clientsNoPackages[2][4]['numClients'] = 0;
		foreach ($tmp as $v) {
			$clientId = $v['c']['clientId'];
			$loaId = $v['l']['loaId'];

			if ($clientId && $loaId) {
				$lastLiveDate = $this->Client->query("SELECT DATEDIFF('$date',MAX(OfferLive.endDate)) as numDays, loaLevelId, membershipPackagesRemaining, membershipBalance
									FROM $offerLive as OfferLive
									INNER JOIN clientLoaPackageRel USING(packageId)
									INNER JOIN loa as Loa USING(loaId)
									WHERE clientLoaPackageRel.clientId = {$clientId} AND loaId = {$loaId} AND OfferLive.endDate <= '$date'
									GROUP BY clientLoaPackageRel.clientId");
				/* if membershipnumpackages has value, remaining > 0 = keep */
				if (empty($lastLiveDate)) {
					continue;
				}
				if ($lastLiveDate[0]['Loa']['loaLevelId'] == 2
					&& ($lastLiveDate[0]['Loa']['membershipPackagesRemaining'] > 0 OR $lastLiveDate[0]['Loa']['membershipBalance'] > 0)) {
					$row = 1;
				} else {
					$row = 2;
				}
				$numDays = @$lastLiveDate[0][0]['numDays'];
				if ($numDays >= 7 AND $numDays <= 13) {
					$col = 1;
				} else if ($numDays >= 14 AND $numDays <= 20) {
					$col = 2;
				} else if ($numDays >= 21 AND $numDays <= 27) {
					$col = 3;
				} else if ($numDays >= 28) {
					$col = 4;
				} else {
					continue;
				}

				$clientsNoPackages[$row][$col]['numClients'] += 1;
				$clientsNoPackages[$row][$col]['clientIds'][] = $clientId;
			}
		}

		$clientsNoPackages['totalClients'] = $tmp2[0][0]['totalClients'];

		$this->set('clientsNoPackages', $clientsNoPackages);
	#### END Inventory Management ####
	}

	function weekly_scorecard() {
		/* 1 - Total */
		$tot = $this->OfferType->query("SELECT
		 weeknumber as col1, weekBeginSunday as col2,
		 packagesSold as col3, packagesSoldYoY as col4,
		 revenueCollected as col5, revenueCollectedYoY as col6,
		 avgSalePriceCollected as col7, avgSalePriceCollectedYoY as col8,
		 revenuetarget
		FROM reporting.weeklyScorecardTotal as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		group by quarter, weekBeginSunday
		ORDER BY weekBeginSunday
		;");
		// QTD Revenue Target
		$tmp = $this->OfferType->query("SELECT
		 SUM(revenuetarget) as quarterRevenueTarget
		FROM reporting.weeklyScorecardTotal as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		group by quarter
		ORDER BY weekBeginSunday
		;");
		//QTD Last Year
		$tmp2 = $this->OfferType->query("SELECT SUM(t.packagesSoldPrevious) as packagesSoldPrevious, SUM(revenuecollectedprevious) as revenueCollectedPrevious,
		SUM(revenuecollectedprevious)/SUM(t.packagesSoldPrevious) AS aspCollectedPrevious
		FROM reporting.weeklyScorecardTotal t
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		;");
		// QTR
		$tmp3 = $this->OfferType->query("SELECT SUM(t.packagesSoldPrevious) as qtr_packagesSoldPrevious, SUM(revenuecollectedprevious) as qtr_revenueCollectedPrevious
		FROM reporting.weeklyScorecardTotal t
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		;");
		$tot[0][0] = $tmp[0][0];
		$this->set('tot', $tot);
		$this->set('totLastYear', array_merge($tmp2[0][0], $tmp3[0][0]));
		$qtr = $this->OfferType->query("SELECT SUM(revenuetarget) as revenueTarget
		FROM reporting.weeklyScorecardTotal
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		ORDER BY weekbeginsunday
		;");
		$this->set('qtr', $qtr);


		/* 2 - Auctions */
		$auc = $this->OfferType->query("select
			weeknumber as col1, weekbeginsunday as col2,
			auctionrevenuepotential as col3, auctionRevenuePotentialYoY as col4,
			auctionslisted as col5, auctionslistedyoy as col6,
			conversionrate as col7, conversionrateyoy as col8,
			successfulauctions as col9, successfulauctionsyoy as col10,
			auctionticketspotential as col11, auctionticketspotentialyoy as col12,
			auctionrevenuecollected as col13, auctionrevenuecollectedyoy as col14,
			percentretailcollected as col15, percentretailcollectedyoy as col16,
			collectionrate as col17, collectionrateyoy as col18,
			auctionticketscollected as col19, auctionticketscollectedyoy as col20,
			avgsalepricecollected as col21, avgsalepricecollectedyoy as col22,
			revenuetarget
		from reporting.weeklyScorecardAuctions  as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		group by quarter, weekBeginSunday
		order by weekBeginSunday;");
		$tmp = $this->OfferType->query("SELECT
		 SUM(revenuetarget) as quarterRevenueTarget
		FROM reporting.weeklyScorecardAuctions as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		group by quarter
		ORDER BY weekBeginSunday
		;");

		$tmp2 = $this->OfferType->query("SELECT SUM(a.auctionsListedPrevious) as auctionsListedPrevious, SUM(a.successfulAuctionsPrevious) as successfulAuctionsPrevious,
		 SUM(a.auctionTicketsPotentialPrevious) as auctionTicketsPotentialPrevious, SUM(a.auctionRevenueCollectedPrevious) as auctionRevenueCollectedPrevious,
		 SUM(a.auctionTicketsCollectedPrevious) as auctionTicketsCollectedPrevious, SUM(a.auctionRevenueCollectedPrevious) / SUM(a.auctionTicketsCollectedPrevious) AS aspPrevious
		FROM reporting.weeklyScorecardAuctions a
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		;
		");
		$tmp3 = $this->OfferType->query("SELECT SUM(a.auctionsListedPrevious) as qtr_auctionsListedPrevious, SUM(a.successfulAuctionsPrevious) as qtr_successfulAuctionsPrevious,
		 SUM(a.auctionTicketsPotentialPrevious) as qtr_auctionTicketsPotentialPrevious, SUM(a.auctionRevenueCollectedPrevious) as qtr_auctionRevenueCollectedPrevious,
			SUM(a.auctionTicketsCollectedPrevious) as qtr_auctionTicketsCollectedPrevious
		FROM reporting.weeklyScorecardAuctions a
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		;
		");

		$auc[0][0] = $tmp[0][0];
		$aucqtr = $this->OfferType->query("SELECT SUM(revenuetarget) as revenueTarget
		FROM reporting.weeklyScorecardAuctions
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		ORDER BY weekbeginsunday
		;");
		$this->set('aucqtr', $aucqtr);
		$this->set('aucLastYear', array_merge($tmp2[0][0], $tmp3[0][0]));
		$this->set('auc', $auc);


		/* 3 - Fixed Price */
		$fp = $this->OfferType->query("select
		 weeknumber as col1, weekbeginsunday as col2,
		 buynowoffers as col3, buynowoffersyoy as col4,
		 numberrequests as col5, numberrequestsyoy as col6,
		 packagessold as col7, packagessoldyoy as col8,
		 collectionrate as col9, collectionrateyoy as col10,
		 revenuecollected as col11, revenuecollectedyoy as col12,
		 avgsalepricecollected as col13, avgsalepricecollectedyoy as col14,
		revenuetarget
		from reporting.weeklyScorecardFixedPrice as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		group by quarter, weekbeginsunday
		order by weekbeginsunday
		;");
		$tmp = $this->OfferType->query("SELECT
		 SUM(revenuetarget) as quarterRevenueTarget
		FROM reporting.weeklyScorecardFixedPrice as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		group by quarter
		ORDER BY weekBeginSunday
		;");

		$tmp2 = $this->OfferType->query("SELECT SUM(a.buyNowOffersPrevious) as buyNowOffersPrevious, SUM(a.numberRequestsPrevious) as numberRequestsPrevious,
		SUM(packagesSoldPrevious) as packagesSoldPrevious, SUM(revenuecollectedprevious) as revenueCollectedPrevious,
		SUM(a.revenueCollectedPrevious) / SUM(a.packagesSoldPrevious) AS aspPrevious
		FROM reporting.weeklyScorecardFixedPrice a
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		AND  YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		;");
		$tmp3 = $this->OfferType->query("SELECT SUM(a.buyNowOffersPrevious) as qtr_buyNowOffersPrevious, SUM(a.numberRequestsPrevious) as qtr_numberRequestsPrevious,
		SUM(packagesSoldPrevious) as qtr_packagesSoldPrevious, SUM(revenuecollectedprevious) as qtr_revenueCollectedPrevious
		FROM reporting.weeklyScorecardFixedPrice a
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		;");

		$fp[0][0] = $tmp[0][0];
		$fpqtr = $this->OfferType->query("SELECT SUM(revenuetarget) as revenueTarget
		FROM reporting.weeklyScorecardFixedPrice
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		ORDER BY weekbeginsunday
		;");
		$this->set('fpqtr', $fpqtr);
		$this->set('fp', $fp);
		$this->set('fpLastYear', array_merge($tmp2[0][0], $tmp3[0][0]));

		/* 8 - Buyers */
		$buyers = $this->OfferType->query("
		select
		 weeknumber as col1, weekBeginSunday as col2,
		 newBuyerActivity as col3, newbuyerYoY as col4,
		 returningBuyerActivity as col5, returningbuyerYoY as col6,
		 totalBuyerActivity as col7, totalbuyerYoY as col8,
		 newBuyerTarget, returningBuyerTarget, totalBuyerTarget
		from reporting.weeklyScorecardBuyers as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		group by quarter, weekbeginsunday
		order by weekbeginsunday
		;");
		$tmp = $this->OfferType->query("SELECT
		 SUM(newbuyertarget) quarterNewBuyerTarget, sum(returningbuyertarget) quarterReturningBuyerTarget, sum(totalbuyertarget) quarterTotalBuyerTarget
		FROM reporting.weeklyScorecardBuyers as data
		WHERE YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())
		group by quarter, YEAR(weekBeginSunday)
		ORDER BY weekBeginSunday
		;");

		$tmp2 = $this->OfferType->query("SELECT SUM(b.newBuyerActivityPrevious) as newBuyerActivityPrevious, SUM(b.returningBuyerActivityPrevious) as returningBuyerActivityPrevious,
		 SUM(b.totalBuyerActivityPrevious) as totalBuyerActivityPrevious
		FROM reporting.weeklyScorecardBuyers b
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		 AND weeknumber < WEEK(NOW())

		;");
		$tmp3 = $this->OfferType->query("SELECT SUM(b.newBuyerActivityPrevious) as qtr_newBuyerActivityPrevious, SUM(b.returningBuyerActivityPrevious) as qtr_returningBuyerActivityPrevious,
		 SUM(b.totalBuyerActivityPrevious) as qtr_totalBuyerActivityPrevious
		FROM reporting.weeklyScorecardBuyers b
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		;");

		$buyers[0][0] = $tmp[0][0];
		$buyerQtr = $this->OfferType->query("SELECT
		SUM(newbuyertarget) quarterNewBuyerTarget, sum(returningbuyertarget) quarterReturningBuyerTarget, sum(totalbuyertarget) quarterTotalBuyerTarget
		FROM reporting.weeklyScorecardBuyers
		WHERE
		 QUARTER(weekBeginSunday) = QUARTER(NOW() - INTERVAL 7 DAY)
		 AND YEAR(weekBeginSunday) = YEAR(NOW() - INTERVAL 7 DAY)
		ORDER BY weekbeginsunday
		;");
		$this->set('buyerQtr', $buyerQtr);
		$this->set('buyers', $buyers);
		$this->set('buyersLastYear', array_merge($tmp2[0][0], $tmp3[0][0]));

		if (isset($this->params['named']['cron'])) {

			Configure::write('debug', 0);
			$this->autoRender = false;
			$this->layout = '';
			$this->render();

            // jw 3/10/11 - switched to PHPMailer for iPad/iPhone fix
			// $to = 'mchoe@luxurylink.com,management@luxurylink.com,jlagraff@luxurylink.com,ahahn@luxurylink.com,kjost@luxurylink.com';
			App::import('Vendor', 'PHPMailer', array('file' => 'phpmailer'.DS.'class.phpmailer.php'));

			$mail = new PHPMailer();
			$mail->From = 'no-reply@toolbox.luxurylink.com';
			$mail->FromName = 'no-reply@toolbox.luxurylink.com';
			$mail->AddAddress('scorecard@luxurylink.com', 'scorecard@luxurylink.com');
			$mail->Subject = 'Weekly Scorecard Report';
			$mail->Body = 'Weekly scorecard report enclosed' . "\n";
			$mail->AddStringAttachment($this->output, 'weekly_scorecard.html', 'base64', 'text/html');
			$result = $mail->Send();

			echo $result ? "Mail sent" : "Mail failed";
			$this->output = '';
		}
	}

	function deal_alert() {
		$db = ConnectionManager::getDataSource('live');

		$top = $db->query("SELECT clientId, client.name, count(*) as n
							FROM dealAlert INNER JOIN client USING(clientId)
							GROUP BY clientId ORDER by n desc");

		$subscribeDates = $db->query("SELECT WEEKOFYEAR(subscribeDate) as theWeek, count(*) as n
								FROM dealAlert INNER JOIN client USING(clientId)
								GROUP BY WEEKOFYEAR(subscribeDate) ORDER by theWeek ASC LIMIT 10 ");

		foreach ($subscribeDates as $v) {
			$numSignups[] = $v[0]['n'];
			$numSignupsWeek[] = $v[0]['theWeek'];
		}

		$numSignups = $this->googleSimpleEncode($numSignups);

		for($i = 0; $i < 10; $i++) {
			$points[] = $top[$i][0]['n'];
			$clients[] = urlencode($top[$i]['client']['name']);
		}
		$this->set(compact('top', 'points', 'clients', 'numSignups', 'numSignupsWeek'));
	}

	function googleSimpleEncode($values, $max = -1, $min = 0) {
	        $encoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	        $chartdata = '';
	        $rangemax = 61;
	        if ($max < 0) {
	                $max = max($values);
	        }
	        if ($max < max($values)) {
	                $max = max($values);
	        }
	        $range = $max - $min;
	        $scale = $rangemax / $range;
	        foreach ($values as $k => $v) {
	                if ($v - $min >= 0) {
	                        $chartdata .= $encoding[floor(($v - $min) * $scale)];
	                } else {
	                        $chartdata .= '_';
	                }
	        }
	        return $chartdata;
	}

    //invoice added by ronayson 11/08/10
    function invoice() {

       if (!empty($this->data)) {

	        if (!empty($this->params['named']['sortBy'])) {
	            $direction = (@$this->params['named']['sortDirection'] == 'DESC') ? 'DESC' : 'ASC';
	            $order = $this->params['named']['sortBy'].' '.$direction;
	            $this->set('sortBy', $this->params['named']['sortBy']);
	            $this->set('sortDirection', $direction);
	        } else {
	            $order = 'Invoice.submittedByDate DESC';
	            $this->set('sortBy', 'Invoice.accountingInvoiceId');
    	        $this->set('sortDirection', 'DESC');
	        }

           $where = "";

           if ($this->params['data']['condition1']['value']['between'][0] && $this->params['data']['condition1']['value']['between'][1]) {
                $date1 = $this->params['data']['condition1']['value']['between'][0];
                $date2 = $this->params['data']['condition1']['value']['between'][1];
                $seachBy = $this->params['data']['OfferType']['searchBy'];
            } else {
                $date1 = $this->data['condition1']['value']['between'][0];
                $date2 = $this->data['condition1']['value']['between'][1];
                $seachBy = $this->data['OfferType']['searchBy'];
            }

            switch ($seachBy) {
                    case 0:
                        $where = " WHERE Invoice.submittedByDate BETWEEN DATE('$date1') AND DATE('$date2')";
                        break;
                    case 1:
                        $where = " WHERE Invoice.checkinDate BETWEEN DATE('$date1') AND DATE('$date2')";
                        break;
                    default:
                        $where = " WHERE Invoice.submittedByDate BETWEEN DATE('$date1') AND DATE('$date2')";
                }

            $sql = "SELECT COUNT(accountingInvoiceId) as numRecords FROM accountingInvoice as Invoice $where";

       	    $results = $this->OfferType->query($sql);

            $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);

            $sql = "SELECT * FROM accountingInvoice as Invoice $where ORDER BY $order LIMIT $this->limit";

	        $results = $this->OfferType->query($sql);

            $this->set('currentPage', $this->page);
            $this->set('numRecords', $numRecords);
            $this->set('numPages', $numPages);
            $this->set('data', $this->data);
	        $this->set('results', $results);
		    $this->set('serializedFormInput', serialize($this->data));

	    }
	}

    //TODO: A lot of duplication of code, use this method as a template for all the others and cut down on the number of times the following code is repeated
	function _build_conditions($data) {
	    $conditions = array();

		if (empty($data)) {
			return false;
		}

	    foreach ($data as $k => $ca) {
	        if (isset($ca['value']['between'])) {
                $betweenCondition = $ca['value']['between'];
            } else {
                $betweenCondition = false;
            }

            /* Check if the conditions have valid data and can be used in a where clause */
	        if (empty($ca['field']) ||
	            empty($ca['value'])) {
	                continue;                               //skip if no valid data found
	            }

            /* If we got this far then that means we have adequate data for a where clause */
            if (is_array($betweenCondition)) {              //check for a condition eligible for BETWEEN
                $firstValue = array_shift($betweenCondition);
                $secondValue = array_shift($betweenCondition);

                if (strlen($firstValue) == 0) {
                    $firstValue = NULL;
                }

                if (strlen($secondValue) == 0) {
                    $secondValue = NULL;
                }
                $betweenCondition = true;
                if (!strlen($firstValue)  && !strlen($secondValue)) {   //if both between values were ommited, it's invalid
                    continue;
                }
            } else {
                unset($firstValue);
                unset($secondValue);
                $betweenCondition = false;
            }

            if (isset($ca['explicit']) && $ca['explicit'] == 'true'):
                $conditions[$k] =   $ca['field'].' '.$ca['value'];
            elseif ($betweenCondition):                                    //generate valid SQL for a between condition
	            if (NULL !== $firstValue && NULL !== $secondValue) {    //if both values were entered, it's a between
	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	            } else {                                                //if only one value was entered, it's not a between
	                $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	            }

	        else:
	            if(is_array($ca['value'])) {
	                //wrap in single quotes
	                foreach ($ca['value'] as $value) {
	                    $values[] = "'{$value}'";
	                }
	                $conditions[$k] =   $ca['field'].' IN('.implode(',', $values).')';
	            } elseif (strpos($ca['field'], 'MATCH=') !== false) {
	                $field = substr($ca['field'], strpos($ca['field'], '=')+1);
	                $conditions[$k] =   'MATCH('.$field.') AGAINST('."'{$ca['value']}' IN BOOLEAN MODE)";
	            } elseif (strpos($ca['field'], 'LIKE=') !== false) {
    	                $field = substr($ca['field'], strpos($ca['field'], '=')+1);
    	                $conditions[$k] =   "{$field} LIKE '%{$ca['value']}%'";
    	        } elseif (strpos($ca['value'], '!=') !== false) {
    	            $value = substr($ca['value'], strpos($ca['value'], '=')+2);
	                $conditions[$k] =   "{$ca['field']} != '{$value}'";
    	        } elseif (strpos($ca['value'], '>') !== false) {
        	            $value = trim(substr($ca['value'], strpos($ca['value'], '>')+1));
    	                $conditions[$k] =   "{$ca['field']} > '{$value}'";
        	    } else {
	                $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }

	        endif; //end generate SQL for between condition
	    }

	    return implode($conditions, ' AND ');

	}

	function merch() {

		$this->Client = new Client();

		if(!empty($this->data['datePicker'])){
			$date = $this->data['datePicker'];
		} else {
			$date = date('Y-m-d');
		}
		$this->set('date', $date);
		$dateTs = strtotime($date);

		$lastMonth = date('Y-m-01', strtotime('-1 month', $dateTs));
		$twoMonthsAgo = date('Y-m-01', strtotime('-2 month', $dateTs));
		$lastMonthLastYear = date('Y-m-01', strtotime('-13 months', $dateTs));

		$yesterday = date('Y-m-d', strtotime('yesterday', $dateTs));

		$lastMonthEnd = date('Y-m-t', strtotime('-1 month', $dateTs));
		$twoMonthsAgoEnd = date('Y-m-t', strtotime('-2 month', $dateTs));
		$lastMonthLastYearEnd = date('Y-m-t', strtotime('-13 months', $dateTs));

		$this->set(compact('lastMonthDisplay', 'twoMonthsAgoDisplay', 'lastMonthLastYearDisplay'));

		$auctions = array(1,2,6);

		if (isset($this->data['site']) && $this->params['data']['site'] == 'family') {
			$siteId = 2;
			$siteName = 'Family';
		} else {
			$siteId = 1;
			$siteName = 'LuxuryLink';
		}

		$siteCondition = "schedulingMaster.siteId = $siteId";
        $loaSiteCondition = "Loa.sites LIKE '%{$siteName}%'";
		//$loaSiteCondition = "EXISTS(SELECT * FROM multiSite WHERE model = 'Loa' and modelId = Loa.loaId and sites LIKE '%$siteName%')";
		$offerLive = "offer$siteName";
		$ticketSiteCondition = "Ticket.siteId = $siteId";

 		$sqlFunctions = new ReportsControllerFunctions();

		# AUCTIONS/FP CLOSING
		/* Auctions/Fp Closing Today */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $date, 1);
		$tmp = $this->Client->query($sql);
		$sales[1][1] = $tmp[0][0]['auctionsClosingCount'];

		/* Auctions/Fp Closing Yesterday */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $yesterday, 1);
		$tmp = $this->Client->query($sql);
		$sales[1][2] = $tmp[0][0]['auctionsClosingCount'];

		/* Auctions/Fp Closing Last 7 Daily Average */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $date, 7);
		$tmp = $this->Client->query($sql);
		$sales[1][3] = $tmp[0][0]['auctionsClosingCount'];

		/* Auctions/Fp Closing Last 30 Daily Average */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $date, 30);
		$tmp = $this->Client->query($sql);
		$sales[1][4] = $tmp[0][0]['auctionsClosingCount'];

		/* Auctions/Fp Closing Last 90 Daily Average */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $date, 90);
		$tmp = $this->Client->query($sql);
		$sales[1][5] = $tmp[0][0]['auctionsClosingCount'];

		/* Auctions/Fp Closing Last 365 Daily Average */
		$sql = $sqlFunctions->sqlAuctionsClosing($siteId, $date, 365);
		$tmp = $this->Client->query($sql);
		$sales[1][6] = $tmp[0][0]['auctionsClosingCount'];
		# END AUCTIONS CLOSING



		# AUCTIONS/FP CLOSING WITH FUNDED TICKETS
		/* Today */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $date, 1);
		$tmp = $this->Client->query($sql);
		$sales[2][1] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][1] = ROUND($sales[2][1]/$sales[1][1]*100,1);
		$sales[4][1] = $tmp[0][0]['auctionsFundedAveragePrice'];

		/* Yesterday */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $yesterday, 1);
		$tmp = $this->Client->query($sql);
		$sales[2][2] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][2] = ROUND($sales[2][2]/$sales[1][2]*100,1);
		$sales[4][2] = $tmp[0][0]['auctionsFundedAveragePrice'];

		/* Last 7 Days Avg */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $date, 7);
		$tmp = $this->Client->query($sql);
		$sales[2][3] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][3] = ROUND($sales[2][3]/$sales[1][3]*100,1);
		$sales[4][3] = $tmp[0][0]['auctionsFundedAveragePrice'];

		/* Last 30 Days Avg */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $date, 30);
		$tmp = $this->Client->query($sql);
		$sales[2][4] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][4] = ROUND($sales[2][4]/$sales[1][4]*100,1);
		$sales[4][4] = $tmp[0][0]['auctionsFundedAveragePrice'];

		/* Last 90 Days Avg */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $date, 90);
		$tmp = $this->Client->query($sql);
		$sales[2][5] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][5] = ROUND($sales[2][5]/$sales[1][5]*100,1);
		$sales[4][5] = $tmp[0][0]['auctionsFundedAveragePrice'];

		/* Last 365 Days Avg */
		$sql = $sqlFunctions->sqlAuctionsFunded($siteId, $date, 365);
		$tmp = $this->Client->query($sql);
		$sales[2][6] = $tmp[0][0]['auctionsFundedCount'];
		$sales[3][6] = ROUND($sales[2][6]/$sales[1][6]*100,1);
		$sales[4][6] = $tmp[0][0]['auctionsFundedAveragePrice'];
		# END AUCTIONS CLOSING WITH FUNDED TICKETS


		# FP Requests
		// today
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $date, 1);
		$tmp = $this->Client->query($sql);
		$sales[5][1] = ROUND($tmp[0][0]['fpRequestCount']);

		// yesterday
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $yesterday, 1);
		$tmp = $this->Client->query($sql);
		$sales[5][2] = ROUND($tmp[0][0]['fpRequestCount']);

		// 7 days
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $date, 7);
		$tmp = $this->Client->query($sql);
		$sales[5][3] = ROUND($tmp[0][0]['fpRequestCount']);

		// 30 days
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $date, 30);
		$tmp = $this->Client->query($sql);
		$sales[5][4] = ROUND($tmp[0][0]['fpRequestCount']);

		// 90 days
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $date, 90);
		$tmp = $this->Client->query($sql);
		$sales[5][5] = ROUND($tmp[0][0]['fpRequestCount']);

		// 365 days
		$sql = $sqlFunctions->sqlFixedPriceRequest($siteId, $date, 365);
		$tmp = $this->Client->query($sql);
		$sales[5][6] = ROUND($tmp[0][0]['fpRequestCount']);
		# END FP Requests


		# FP Funded
		// today
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $date, 1);
		$tmp = $this->Client->query($sql);
		$sales[6][1] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][1] = ROUND($tmp[0][0]['fpFundedAveragePrice']);

		// yesterday
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $yesterday, 1);
		$tmp = $this->Client->query($sql);
		$sales[6][2] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][2] = ROUND($tmp[0][0]['fpFundedAveragePrice']);

		// 7 days
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $date, 7);
		$tmp = $this->Client->query($sql);
		$sales[6][3] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][3] = ROUND($tmp[0][0]['fpFundedAveragePrice']);

		// 30 days
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $date, 30);
		$tmp = $this->Client->query($sql);
		$sales[6][4] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][4] = ROUND($tmp[0][0]['fpFundedAveragePrice']);

		// 90 days
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $date, 90);
		$tmp = $this->Client->query($sql);
		$sales[6][5] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][5] = ROUND($tmp[0][0]['fpFundedAveragePrice']);

		// 365 days
		$sql = $sqlFunctions->sqlFixedPriceFunded($siteId, $date, 365);
		$tmp = $this->Client->query($sql);
		$sales[6][6] = ROUND($tmp[0][0]['fpFundedCount']);
		$sales[7][6] = ROUND($tmp[0][0]['fpFundedAveragePrice']);
		# END FP Requests



		# TRAVEL REVENUE [ALEE] CHANGED HOW THIS IS CALCULATED REQUEST BY MCHOE JAN 22-2010
		$sales[8][1] = ($sales[2][1] * $sales[4][1]) + ($sales[6][1] * $sales[7][1]);
		$sales[8][2] = ($sales[2][2] * $sales[4][2]) + ($sales[6][2] * $sales[7][2]);
		$sales[8][3] = ($sales[2][3] * $sales[4][3]) + ($sales[6][3] * $sales[7][3]);
		$sales[8][4] = ($sales[2][4] * $sales[4][4]) + ($sales[6][4] * $sales[7][4]);
		$sales[8][5] = ($sales[2][5] * $sales[4][5]) + ($sales[6][5] * $sales[7][5]);
		$sales[8][6] = ($sales[2][6] * $sales[4][6]) + ($sales[6][6] * $sales[7][6]);
		# END TRAVEL REVENUE

		$this->set('sales', $sales);

		#### END SALES ####


		#### Revenue ####
		$mtdStart = date('Y-m-01', strtotime($date));
		if (date('Y-m-d', strtotime($date)) < date('Y-04-01', strtotime($date))) {
			$qtdStart = date('Y-01-01', strtotime($date));
		} else if (date('Y-m-d', strtotime($date)) < date('Y-07-01', strtotime($date))) {
			$qtdStart = date('Y-04-01', strtotime($date));
		} else if (date('Y-m-d', strtotime($date)) < date('Y-10-01', strtotime($date))) {
			$qtdStart = date('Y-07-01', strtotime($date));
		} else {
			$qtdStart = date('Y-10-01', strtotime($date));
		}
		$ytdStart = date('Y-01-01', strtotime($date));

		$revenueMtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket WHERE Ticket.created >= '$mtdStart' AND Ticket.created <= '$date' AND $ticketSiteCondition AND Ticket.ticketStatusId NOT IN (7,8,17) AND Ticket.ticketId IN (SELECT ticketId FROM paymentDetail WHERE isSuccessfulCharge = 1)");
		$revenueQtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket WHERE Ticket.created >= '$qtdStart' AND Ticket.created <= '$date' AND $ticketSiteCondition AND Ticket.ticketStatusId NOT IN (7,8,17) AND Ticket.ticketId IN (SELECT ticketId FROM paymentDetail WHERE isSuccessfulCharge = 1)");
		$revenueYtd = $this->Client->query("SELECT SUM(Ticket.billingPrice) as revenue FROM ticket as Ticket WHERE Ticket.created >= '$ytdStart' AND Ticket.created <= '$date' AND $ticketSiteCondition AND Ticket.ticketStatusId NOT IN (7,8,17) AND Ticket.ticketId IN (SELECT ticketId FROM paymentDetail WHERE isSuccessfulCharge = 1)");
		$this->set(compact('revenueMtd', 'revenueQtd', 'revenueYtd'));
		#### End Revenue ####
	#### BEGIN AGING ####
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT clientId) as clients, COUNT(DISTINCT clientId) as numClients,
										IF(DATEDIFF(NOW(), startDate) > 121, 3, IF(DATEDIFF(NOW(), startDate) > 91, 2, 1)) as severity
										FROM loa AS Loa
										WHERE Loa.startDate < NOW() - INTERVAL 60 DAY
												AND Loa.inactive <> 1 AND Loa.endDate >= NOW()
												AND Loa.membershipBalance > 0 AND $loaSiteCondition
										GROUP BY severity");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT clientId) as numClients,
										IF(DATEDIFF(NOW(), startDate) > 121, 3, IF(DATEDIFF(NOW(), startDate) > 91, 2, 1)) as severity
										FROM loa AS Loa
										WHERE Loa.startDate < NOW() - INTERVAL 60 DAY
												AND Loa.inactive <> 1 AND Loa.endDate >= NOW() AND $loaSiteCondition
										GROUP BY severity");

		foreach ($tmp as $v) {
			$aging[$v[0]['severity']] = @$v[0];
		}
		foreach ($tmp2 as $v) {
			@$aging[$v[0]['severity']]['totalClients'] += @$v[0]['numClients'];
		}

		$this->set('aging', $aging);

	#### END AGING ####


	#### Inventory Management ####
		/* Distressed Auctions */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT schedulingMaster.schedulingMasterId) as ids,
								COUNT(DISTINCT schedulingMaster.schedulingMasterId) as numOffers,
								IF(numOffersNoBid >10, 2, 1) as severity,
								expirationCriteriaId FROM schedulingMasterPerformance
								INNER JOIN schedulingMaster USING(schedulingMasterId)
								INNER JOIN schedulingInstance ON(schedulingInstance.schedulingMasterId = schedulingMaster.schedulingMasterId AND schedulingInstance.endDate >= NOW())
								LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingMaster.schedulingMasterId)
								LEFT JOIN track USING(trackId)
								WHERE numOffersNoBid >= 5 AND $siteCondition
								GROUP BY severity, expirationCriteriaId");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT schedulingMaster.schedulingMasterId) as totalNumOffers,
											expirationCriteriaId FROM schedulingMasterPerformance
											INNER JOIN schedulingMaster USING(schedulingMasterId)
											INNER JOIN schedulingInstance ON(schedulingInstance.schedulingMasterId = schedulingMaster.schedulingMasterId AND schedulingInstance.endDate >= NOW())
											LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingMaster.schedulingMasterId)
											LEFT JOIN track USING(trackId)
											WHERE $siteCondition
											GROUP BY expirationCriteriaId");

		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];

			@$distressedAuctions[$row][$col]['numOffers'] += $v[0]['numOffers'];
			@$distressedAuctions[$row][$col]['ids'][] = $v[0]['ids'];
		}

		foreach ($tmp2 as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			@$distressedAuctions[$row]['totalNumOffers'] += $v[0]['totalNumOffers'];
		}

		$this->set('distressedAuctions', $distressedAuctions);

		/* Distressed Buy Nows */
		$tmp = $this->Client->query("SELECT
								GROUP_CONCAT(DISTINCT schedulingInstance.schedulingMasterId) as ids,
								COUNT(DISTINCT OfferLive.offerId) numOffers,
								IF(
									DATEDIFF('$date', Ticket.created) >= 43,
									2,
										IF(DATEDIFF('$date', Ticket.created) >= 21,
										1,
										0)
									) as severity,
								expirationCriteriaId
								FROM $offerLive AS OfferLive
								INNER JOIN offer USING(offerId)
								INNER JOIN schedulingInstance USING(schedulingInstanceId)
								LEFT JOIN ticket AS Ticket USING(offerId)
								LEFT JOIN schedulingMasterTrackRel ON(schedulingMasterTrackRel.schedulingMasterId = schedulingInstance.schedulingMasterId)
								LEFT JOIN track USING(trackId)
								WHERE OfferLive.offerTypeId IN(3,4) AND OfferLive.endDate >= NOW()
								GROUP BY severity, expirationCriteriaId");

		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];

			@$distressedBuyNows[$row][$col]['numOffers'] += $v[0]['numOffers'];
			@$distressedBuyNows[$row][$col]['ids'][] = $v[0]['ids'];
		}

		$this->set('distressedBuyNows', $distressedBuyNows);

		/* Packages with x days Validity Left */
		$tmp = $this->Client->query("SELECT 
GROUP_CONCAT(Package.packageId) AS ids, 
COUNT(Package.packageId) AS numPackages,
IF(DATEDIFF(pricePoint.validityEnd, '$date') < 30, 3, IF(DATEDIFF(pricePoint.validityEnd, '$date') < 45, 2, 1)) AS severity, expirationCriteriaId
FROM package AS Package 
INNER JOIN pricePoint USING (packageId)
INNER JOIN clientLoaPackageRel USING(packageId) 
INNER JOIN loa AS Loa USING(loaId) 
INNER JOIN track USING(loaId) 
WHERE (pricePoint.validityEnd BETWEEN NOW() AND NOW() + INTERVAL 60 DAY)
AND (NOW() BETWEEN Loa.startDate AND Loa.endDate) AND Loa.loaLevelId IN (1,2) AND Loa.inactive = 0
AND $loaSiteCondition GROUP BY severity, expirationCriteriaId");
		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			$col = $v[0]['severity'];
			@$expiringPackages[$row][$col]['numPackages'] += $v[0]['numPackages'];
			@$expiringPackages[$row][$col]['ids'][] = $v[0]['ids'];
		}

		$this->set('expiringPackages', @$expiringPackages);

		/* Auctions w/o buy now */
		$tmp = $this->Client->query("SELECT GROUP_CONCAT(DISTINCT Package.packageId) AS ids, COUNT(DISTINCT Package.packageId) AS numPackages,
		 								expirationCriteriaId
										FROM package AS Package
										INNER JOIN $offerLive AS OfferLive2 ON(OfferLive2.packageId = Package.packageId AND OfferLive2.isClosed <> 1 AND OfferLive2.offerTypeId IN(1,2,6))
										LEFT JOIN $offerLive AS OfferLive ON(OfferLive.packageId = Package.packageId AND OfferLive.isClosed <> 1 AND OfferLive.offerTypeId IN(3,4))
										INNER JOIN clientLoaPackageRel cl ON(cl.packageid = Package.packageId)
										INNER JOIN loa AS Loa USING(loaId)
										INNER JOIN track USING(trackId)
										WHERE Package.endDate >= '$date' AND OfferLive.offerId IS NULL
										GROUP BY expirationCriteriaId
										");
		foreach ($tmp as $v) {
			if($v['track']['expirationCriteriaId'] == 1 || $v['track']['expirationCriteriaId'] == 4 || $v['track']['expirationCriteriaId'] == 6) {
				$row = 1;
			} else {
				$row = 2;
			}

			@$noBuyNows[$row][1]['numPackages'] += $v[0]['numPackages'];
			@$noBuyNows[$row][1]['ids'][] = $v[0]['ids'];
		}

		$this->set('noBuyNows', $noBuyNows);

		/* Clients with No Packages */
		//get all clients without packages
		$tmp = $this->Client->query("CALL clientsNoScheduledOffersLOA('$date','$date')");
		$tmp2 = $this->Client->query("SELECT COUNT(DISTINCT Loa.clientId) as totalClients FROM loa AS Loa WHERE Loa.inactive <> 1
									 		AND Loa.endDate >= '$date' AND $loaSiteCondition");

		//get the date of the last live package
		$clientsNoPackages[1][1]['numClients'] = 0;
		$clientsNoPackages[1][2]['numClients'] = 0;
		$clientsNoPackages[1][3]['numClients'] = 0;
		$clientsNoPackages[1][4]['numClients'] = 0;
		$clientsNoPackages[2][1]['numClients'] = 0;
		$clientsNoPackages[2][2]['numClients'] = 0;
		$clientsNoPackages[2][3]['numClients'] = 0;
		$clientsNoPackages[2][4]['numClients'] = 0;
		foreach ($tmp as $v) {
			$clientId = $v['c']['clientId'];
			$loaId = $v['l']['loaId'];

			if ($clientId && $loaId) {
				$lastLiveDate = $this->Client->query("SELECT DATEDIFF('$date',MAX(OfferLive.endDate)) as numDays, loaLevelId, membershipPackagesRemaining, membershipBalance
									FROM $offerLive as OfferLive
									INNER JOIN clientLoaPackageRel USING(packageId)
									INNER JOIN loa as Loa USING(loaId)
									WHERE clientLoaPackageRel.clientId = {$clientId} AND loaId = {$loaId} AND OfferLive.endDate <= '$date'
									GROUP BY clientLoaPackageRel.clientId");
				/* if membershipnumpackages has value, remaining > 0 = keep */
				if (empty($lastLiveDate)) {
					continue;
				}
				if ($lastLiveDate[0]['Loa']['loaLevelId'] == 2
					&& ($lastLiveDate[0]['Loa']['membershipPackagesRemaining'] > 0 OR $lastLiveDate[0]['Loa']['membershipBalance'] > 0)) {
					$row = 1;
				} else {
					$row = 2;
				}
				$numDays = @$lastLiveDate[0][0]['numDays'];
				if ($numDays >= 7 AND $numDays <= 13) {
					$col = 1;
				} else if ($numDays >= 14 AND $numDays <= 20) {
					$col = 2;
				} else if ($numDays >= 21 AND $numDays <= 27) {
					$col = 3;
				} else if ($numDays >= 28) {
					$col = 4;
				} else {
					continue;
				}

				$clientsNoPackages[$row][$col]['numClients'] += 1;
				$clientsNoPackages[$row][$col]['clientIds'][] = $clientId;
			}
		}

		$clientsNoPackages['totalClients'] = $tmp2[0][0]['totalClients'];

		$this->set('clientsNoPackages', $clientsNoPackages);
	#### END Inventory Management ####
	}


	function car() {
	    if (!empty($this->data) || !empty($this->params['named']['clientId'])) {

	        $clientId = (!empty($this->params['named']['clientId'])) ? $this->params['named']['clientId'] : $this->data['Client']['clientName_id'];
			$versionArray = ($this->data['Client']['site'] == 'combined') ? array('luxurylink', 'family') : array($this->data['Client']['site']);

	        // 03/22/11 jw - put the entire process in a loop so it can be run for multiple sites if necessary
			foreach ($versionArray as $version) {

				$tableConsolidatedView = ($version == 'family') ? 'carConsolidatedViewFg' : 'carConsolidatedView';
				$stats = $this->OfferType->query("SELECT DATE_FORMAT(CONCAT(year2,'-',month2,'-1'), '%Y%m' ) as yearMonth,
															phone, webRefer, productView, searchView, destinationView, email, event12
													FROM reporting.$tableConsolidatedView AS rs
													WHERE CURDATE() - INTERVAL 14 MONTH <= DATE_FORMAT(CONCAT(year2,'-',month2,'-1'), '%Y-%m-%d')
													AND rs.clientId = '$clientId'
													ORDER BY DATE_FORMAT(CONCAT(year2,'-',month2,'-1'), '%Y-%m-%d') ");

				$tableAuctionSold = ($version == 'family') ? 'carAuctionSoldFg' : 'carAuctionSold';
				$auctions = $this->OfferType->query("SELECT DATE_FORMAT(auc.firstTicketDate, '%Y%m' ) as yearMonth, tickets as aucTickets, revenue as aucRevenue, roomNights as aucNights
														FROM reporting.$tableAuctionSold AS auc
														WHERE CURDATE() - INTERVAL 14 MONTH <= firstTicketDate
														AND auc.clientid = '$clientId'
														ORDER BY firstTicketDate ");

				$tableFixedPriceSold = ($version == 'family') ? 'carFixedPriceSoldFg' : 'carFixedPriceSold';
				$fixedprice = $this->OfferType->query("SELECT DATE_FORMAT(fp.firstTicketDate, '%Y%m' ) as yearMonth, tickets as fpTickets, revenue as fpRevenue, roomNights as fpNights
														FROM reporting.$tableFixedPriceSold AS fp
														WHERE CURDATE() - INTERVAL 14 MONTH <= firstTicketDate
														AND fp.clientid = '$clientId'
														ORDER BY firstTicketDate ");

				$tableAuction = ($version == 'family') ? 'carAuctionFg' : 'carAuction';
				$auctionsTotal = $this->OfferType->query("SELECT DATE_FORMAT(auc.minStartDate, '%Y%m' ) as yearMonth, numberAuctions
														FROM reporting.$tableAuction AS auc
														WHERE CURDATE() - INTERVAL 14 MONTH <= minStartDate
														AND auc.clientid = '$clientId'
														ORDER BY minStartDate ");

				$tableFixedPricePackage = ($version == 'family') ? 'carFixedPricePackageFg' : 'carFixedPricePackage';
				$fixedpriceTotal = $this->OfferType->query("SELECT DATE_FORMAT(fp.lastUpdate, '%Y%m' ) as yearMonth, numberPackages
														FROM reporting.$tableFixedPricePackage AS fp
														WHERE CURDATE() - INTERVAL 14 MONTH <= lastUpdate
														AND fp.clientid = '$clientId'
														ORDER BY lastUpdate ");

				$tableHotelOffer = ($version == 'family') ? 'carHotelOfferFg' : 'carHotelOffer';
				$hotelOfferTotal = $this->OfferType->query("SELECT DATE_FORMAT(ho.snapShotDate, '%Y%m' ) as yearMonth, numberOffers
														FROM reporting.$tableHotelOffer AS ho
														WHERE CURDATE() - INTERVAL 14 MONTH <= snapShotDate
														AND ho.clientid = '$clientId'
														ORDER BY snapShotDate ");

				//setup array of all months we are using in this view
	            //03/22/11 jw - removed 13th month back / added current month
				$today = strtotime(date('Y-m-01'));
				for ($i = 0; $i <= 12; $i++) {
					$ts = strtotime("-".(12-($i))." months", $today);

					$months[$i] = date('Ym', $ts);
					if ($i == 12) {
						$monthNames[$i] = 'Current Month';
					} else {
						$monthNames[$i] = date('M Y', $ts);
					}
				}


				//03/22/11 jw - initialize arrays in case we loop
				$auctionsKeyed = array();
				$fpKeyed = array();
				$aucTotKeyed = array();
				$fpTotKeyed = array();
				$hotelOfferKeyed = array();
				$keyedStats = array();

				foreach($auctions as $k => $v):
					$auctionsKeyed[$v[0]['yearMonth']] = $v['auc'];     //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				foreach($fixedprice as $k => $v):
					$fpKeyed[$v[0]['yearMonth']] = $v['fp'];           //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				foreach($auctionsTotal as $k => $v):
					$aucTotKeyed[$v[0]['yearMonth']] = $v['auc'];           //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				foreach($fixedpriceTotal as $k => $v):
					$fpTotKeyed[$v[0]['yearMonth']] = $v['fp'];           //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				foreach($hotelOfferTotal as $k => $v):
					$hotelOfferKeyed[$v[0]['yearMonth']] = $v['ho'];           //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				//sum the totals for the last 12 months and put everything in an array we can easily reference later
				foreach($stats as $k => $v):
					$keyedStats[$v[0]['yearMonth']] = array_merge($v['rs'], $v[0]);           //set the key for each to the year and month so we can iterate through it easily
				endforeach;

				$totals['phone'] = 0;
				$totals['webRefer'] = 0;
				$totals['productView'] = 0;
				$totals['searchView'] = 0;
				$totals['destinationView'] = 0;
				$totals['email'] = 0;
				$totals['event12'] = 0;
				$totals['aucTickets'] = 0;
				$totals['aucRevenue'] = 0;
				$totals['aucNights'] = 0;
				$totals['fpTickets'] = 0;
				$totals['fpRevenue'] = 0;
				$totals['fpNights'] = 0;
				$totals['aucTotals'] = 0;
				$totals['fpTotals'] = 0;
				$totals['hotelOfferTotal'] = 0;

				foreach ($months as $k => $month):
					$keyedResults[$month] = array_merge((array)@$keyedStats[$month],
													(array)@$auctionsKeyed[$month],
													(array)@$fpKeyed[$month],
													(array)@$aucTotKeyed[$month],
													(array)@$fpTotKeyed[$month],
													(array)@$hotelOfferKeyed[$month]
													);

					//only count the last 12 months in the totals
					//03/22/11 jw - switched k check from 0 to 12 to account adding current month / removing 13 months back
					if ($k == 12) {
						continue;
					}
					$totals['phone'] += @$keyedResults[$month]['phone'];
					$totals['webRefer'] += @$keyedResults[$month]['webRefer'];
					$totals['productView'] += @$keyedResults[$month]['productView'];
					$totals['searchView'] += @$keyedResults[$month]['searchView'];
					$totals['destinationView'] += @$keyedResults[$month]['destinationView'];
					$totals['email'] += @$keyedResults[$month]['email'];
					$totals['event12'] += @$keyedResults[$month]['event12'];
					$totals['aucTickets'] += @$keyedResults[$month]['aucTickets'];
					$totals['aucRevenue'] += @$keyedResults[$month]['aucRevenue'];
					$totals['aucNights'] += @$keyedResults[$month]['aucNights'];
					$totals['fpTickets'] += @$keyedResults[$month]['fpTickets'];
					$totals['fpRevenue'] += @$keyedResults[$month]['fpRevenue'];
					$totals['fpNights'] += @$keyedResults[$month]['fpNights'];
					$totals['aucTotals'] += @$keyedResults[$month]['numberAuctions'];
					$totals['fpTotals'] += @$keyedResults[$month]['numberPackages'];
					$totals['hotelOfferTotal'] += @$keyedResults[$month]['numberOffers'];
				endforeach;

				$results = $keyedResults;

				$versionResults[] = $results;
				$versionTotals[] = $totals;
			}

			//03/22/11 jw - do we need to combine data?
			if (sizeof($versionResults) > 1) {

				$sumResults = array();
				foreach ($versionResults as $k=>$subArray) {
					foreach ($months as $kMonth => $month) {
						if(!isset($sumResults[$month])) { $sumResults[$month] = array(); }
						foreach ($subArray[$month] as $id=>$value) {
							if(!isset($sumResults[$month][$id])) { $sumResults[$month][$id] = 0; }
							$sumResults[$month][$id]+=$value;
						}
					}
				}
				$results = $sumResults;

				$sumTotals = array();
				foreach ($versionTotals as $k=>$subArray) {
					foreach ($subArray as $id=>$value) {
						if(!isset($sumTotals[$id])) { $sumTotals[$id] = 0; }
						$sumTotals[$id]+=$value;
					}
				}
				$totals = $sumTotals;

			}
			// end combining data

            $client = new Client;
            $client->recursive = -1;
            $clientDetails = $client->read(null, $clientId);

            $client->Loa->recursive = -1;
            $loa = $client->Loa->find('first', array('conditions' => array('Loa.clientId' => $client->id)));

            $clientDetails['Loa'] = $loa['Loa'];

            $this->set(compact('results', 'totals', 'months', 'monthNames', 'clients', 'clientDetails'));
	    }
	}

	function car_import() {
		$action = isset($this->params['url']['|GO|']) ? $this->params['url']['|GO|'] : '';
		if ($action == 'download') {
			$this->CarDataImporter->downloadNewFiles();
		}
		if ($action == 'import') {
		    if (isset($this->params['url']['del'])) {
		        $this->CarDataImporter->setDeleteSkippedFlag();
		    }
			$this->CarDataImporter->importPendingFiles();
		}
		$pending = $this->CarDataImporter->getPendingInfo();
		$this->set('pendingFileCount', $pending['pendingFileCount']);
		$this->set('pendingRecordCount', $pending['pendingRecordCount']);
		$this->set('messages', $this->CarDataImporter->getMessages());
	}
	
	/**
	 * 
	 */
	function experiments($experiment_id = null)
	{
		$site_id = (isset($_POST['site_id']) AND $_POST['site_id'] > 0) ? $_POST['site_id'] : null; 
		$experiments = array();
		$this->loadModel('Experiment');

		if (is_null($experiment_id)) { // list all experiments
			$experiments = $this->Experiment->listExperiments($site_id);
			$this->set('experiments', $experiments);
		} else { // display experiment results
			$results = $this->Experiment->getResults((int) $experiment_id);
			$this->set('results', $results);
		}
	}
	
	/**
	 * Ajax method to update experiment status
	 * 
	 * @access	public
	 * @param	int
	 * @param	int
	 */
	public function experiment_status($experiment_id, $status_id)
	{
		$this->autoRender = false;
		$this->loadModel('Experiment');
		$this->Experiment->updateStatus($experiment_id, $status_id);
	}
	
	/**
	 * 
	 */
	public function statement_of_account($report_data = null)
	{
		$service_base = 'http://192.168.100.115/query/';
		$service_url = '';
		$service_urls = array();
		$properties = '';
		
		if ($this->data) {
			$start_date = $this->data['start_date'];
			$end_date = $this->data['end_date'];
			
			if (!isset($this->data['Properties'])) {
				$service_url = $service_base . "properties/$start_date/$end_date";
				$properties = json_decode(file_get_contents($service_url));
				$this->set('properties', $properties);
			} else {
				foreach($this->data['Properties'] as $property_code) {
					$service_url = $service_base . 'index/' . "$property_code/$start_date/$end_date";
					$service_urls[] = array(
						'html' => $service_url,
						'pdf' => $this->webroot . 'reports/soa_pdf/' . base64_encode($service_url)
					);				
				}
				$this->set('report_links', $service_urls);	
			}
		} else {
			$cur_day = date('j');
			$cur_month = date('m');
			$cur_year = date('Y');
			$start_date = $cur_month . '-1-' . $cur_year;
			$end_date = $cur_month . '-' . $cur_day . '-' . $cur_year;

			$service_url = $service_base . "properties/$start_date/$end_date";
			$properties = json_decode(file_get_contents($service_url));
			$this->set('properties', $properties);
		}
		
		$this->set('start_date', $start_date);
		$this->set('end_date', $end_date);
	}

	/**
	 * 
	 */
	public function soa_pdf($data_hash)
	{
		App::import('Vendor', 'DOMPDF', array('file' => 'dompdf-0.6b2' . DS . 'dompdf_config.inc.php'));
		$this->layout = 'ajax';
		$this->autoRender = false;
		$service_url = base64_decode($data_hash);
		$html_data = file_get_contents($service_url);
		$dompdf = new DOMPDF();
		$dompdf->load_html($html_data);
		$dompdf->set_paper('8.5x11', 'landscape');
		$dompdf->render();
		$dompdf->stream('sample.pdf');
	}
	
	/**
	 * 
	 */
	public function consolidated_report($client_id = null)
	{
		Configure::write('debug', 1);
		$this->layout = 'excel';

		$template = APP . 'vendors/consolidated_report/template.xlsx';
		$newFile = TMP . 'consolidated_report.xlsx';
		$outputFile = TMP . 'consolidated_report_output.xlsx';
		$filename = 'consolidated_report_' . date('YmdHis') . '.xlsx';

		$phpExcelVersion = '1.7.6';
		//App::import('Vendor', 'PHPExcel', array('file' => "PHPExcel-$phpExcelVersion" . DS . 'PHPExcel.php'));
		//App::import('Vendor', 'PHPExcel', array('file' => "PHPExcel-$phpExcelVersion" . DS . 'Reader' . DS . 'Excel2007.php'));
		//App::import('Vendor', 'PHPExcel', array('file' => "PHPExcel-$phpExcelVersion" . DS . 'Writer' . DS . 'Excel2007.php'));
		App::import('Vendor', 'ConsolidatedReport', array('file' => 'consolidated_report' . DS . 'consolidated_report.php'));

		$report = new ConsolidatedReport($template, $newFile, $outputFile, $filename);
		$report->test();
		$report->writeSpreadsheetObjectToFile();
		
		// Set our view data
		$this->set('spreadsheet', $report->getSpreadsheetData());
		$this->set('filename', $filename);
	}
}


class ReportsControllerFunctions {

	function sqlAuctionsClosing($siteId, $date, $days) {
		$interval = $days - 1;
		$tableName = ($siteId == 1) ? 'offerLuxuryLink' : 'offerFamily';
		$sql = "SELECT ROUND(COUNT(*)/$days) as auctionsClosingCount
				FROM $tableName
				WHERE endDate BETWEEN '$date' - INTERVAL $interval DAY AND '$date' + INTERVAL 1 DAY
				AND offerTypeId IN (1,2,6)";
		return $sql;
	}

	function sqlAuctionsFunded($siteId, $date, $days) {
		$interval = $days - 1;
		$sql = "SELECT COUNT(ticketId)/$days as auctionsFundedCount, AVG(billingPrice) as auctionsFundedAveragePrice
				FROM ticket
				WHERE siteId = $siteId
				AND ticketStatusId NOT IN (7,8,17)
				AND offerTypeId in (1,2,6)
				AND created BETWEEN '$date' - INTERVAL $interval DAY AND '$date' + INTERVAL 1 DAY
				AND ticketId IN (SELECT ticketId FROM paymentDetail WHERE isSuccessfulCharge = 1)";
		return $sql;
	}

	function sqlFixedPriceRequest($siteId, $date, $days) {
		$interval = $days - 1;
		$sql = "SELECT COUNT(ticketId)/$days AS fpRequestCount
					FROM ticket
					WHERE offerTypeId IN (3, 4)
					AND siteId = $siteId
					AND created BETWEEN '$date' - INTERVAL $interval DAY AND '$date' + INTERVAL 1 DAY";
		return $sql;
	}

	function sqlFixedPriceFunded($siteId, $date, $days) {
		$interval = $days - 1;
		$sql = "SELECT COUNT(ticketId)/$days AS fpFundedCount, AVG(billingPrice) as fpFundedAveragePrice
					FROM ticket
					WHERE offerTypeId IN (3, 4)
					AND siteId = $siteId
					AND ticketStatusId NOT IN (7,8,17)
					AND created BETWEEN '$date' - INTERVAL $interval DAY AND '$date' + INTERVAL 1 DAY
					AND ticketId IN (SELECT ticketId FROM paymentDetail WHERE isSuccessfulCharge = 1)";
		return $sql;
	}
}

?>
