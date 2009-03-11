<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('OfferType');
	var $helpers = array('Pagination');
	//TODO: Add sorting, speed up the sql by adding indexes or a loading splash page, double check accuracy of data
	
	var $page;
	var $limit;
	var $perPage = 20;
	
	function beforeFilter() {
	    parent::beforeFilter();
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
	                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
	                LEFT JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);
                
	        $sql = "SELECT
	                SchedulingInstance.schedulingInstanceId, (SchedulingInstance.endDate >= NOW()) AS offerStatus, SchedulingInstance.startDate, SchedulingInstance.endDate,
	                Client.clientId, Client.name,
	                OfferType.offerTypeName,
	                Offer.offerId,
	                Package.packageId, Package.numNights, Package.approvedRetailPrice, Package.validityEndDate,
	                COUNT(Bid.bidId) as numberOfBids,
	                SchedulingMaster.schedulingMasterId, SchedulingMaster.openingBid, SchedulingMaster.packageName,
	                Loa.loaId, Loa.endDate, Loa.membershipBalance,
	                auction_mstr.auction_wholesale AS remitStatus,
	                (SELECT COUNT(*) 
	                    FROM schedulingInstance AS SchedulingInstance2
	                    INNER JOIN schedulingMaster AS SchedulingMaster2 
                        ON (SchedulingInstance2.schedulingMasterId = SchedulingMaster2.schedulingMasterId)
                        WHERE SchedulingMaster2.schedulingMasterId = SchedulingMaster.schedulingMasterId AND SchedulingInstance2.endDate >= NOW()
	                ) AS futureInstances,
	                Client.managerUsername,
	                IF((Package.validityEndDate - INTERVAL 60 DAY) <= NOW(), 1, 0) as validityEndApproaching,
	                IF((Loa.endDate - INTERVAL 14 DAY) <= NOW(), 1, 0) as loaEndApproaching,
	                IF(SchedulingMasterPerformance.numOffersNoBid >= 10, 1, 0) as flagBids,
	                Track.applyToMembershipBal
	                FROM offer AS Offer
	                LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
	                INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
	                INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
	                LEFT JOIN schedulingMasterPerformance AS SchedulingMasterPerformance ON (SchedulingMasterPerformance.schedulingMasterId = SchedulingMaster.schedulingMasterId)
	                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                LEFT JOIN track AS Track ON (Track.trackid = ClientLoaPackageRel.trackId)
	                LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
	                LEFT JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions
	                GROUP BY Offer.offerId, SchedulingMaster.schedulingMasterId
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
	                    $conditions[$k] = "SchedulingInstance.startDate <= '$firstValue' AND SchedulingInstance.endDate >= '$secondValue'";
	                } else {
    	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	                }
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
                        INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                        INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                        LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
                        INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                        INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                        LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
                    WHERE $conditions
                    GROUP BY Offer.offerId, Client.clientId";

	        $results = $this->OfferType->query($count);
	        $numRecords = count($results);
            $numPages = ceil($numRecords / $this->perPage);

	        $sql = "SELECT
                            Offer.offerId,
                        	GROUP_CONCAT(Client.name) as clientNames,
                        	auction_mstr.auction_wholesale as remitStatus,
                        	#Track.applyToMembershipBal,
                        	OfferType.offerTypeName,
            				(SELECT Country.countryName FROM country AS Country WHERE Country.countryId = Client.countryId) AS country,
            				(SELECT State.stateName FROM state AS State WHERE State.stateId = Client.stateId) AS state,
                        	(SELECT City.cityName FROM city AS City WHERE City.cityId = Client.cityId) AS city,
                            (SchedulingMaster.openingBid / OfferLive.retailValue * 100) AS percentMinBid,
                            (Ticket.billingPrice / OfferLive.retailValue * 100) AS percentClose,
                        	OfferLive.retailValue,
                        	OfferLive.roomNights,
                        	SchedulingInstance.endDate,
                        	COUNT(Bid.bidId) AS numBids,
                        	COUNT(DISTINCT Bid.userId) AS uniqueBids,
                        	COUNT(DISTINCT Ticket.ticketId) AS numTickets,
                        	(SELECT SUM(Ticket3.billingPrice) FROM ticket AS Ticket3 WHERE Ticket3.offerId = Offer.offerId) as moneyPotential,
                        	COUNT(DISTINCT Ticket2.ticketId) AS numTicketsCollected,
                        	(SELECT SUM(Ticket4.billingPrice) FROM ticket AS Ticket4 WHERE Ticket4.offerId = Offer.offerId AND Ticket4.ticketStatusId IN(5,6)) as moneyCollected
                    FROM offer AS Offer
                    LEFT JOIN ticket AS Ticket ON (Ticket.offerId = Offer.offerId)
                    LEFT JOIN ticket AS Ticket2 ON (Ticket2.offerId = Offer.offerId AND Ticket2.ticketStatusId IN(5,6))
                    LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
                    INNER JOIN offerLive AS OfferLive ON (OfferLive.offerId = Offer.offerId)
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
                    INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                    INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                    LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                    LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
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
                                LEFT JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                                LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Ticket.packageId)
                                LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
                                LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId AND PaymentDetail.userId = Ticket.userId)
                    WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);
                
	        $sql = "SELECT
                                        Offer.offerId,
                                        Ticket.ticketId,
                                        GROUP_CONCAT(Client.clientId) as clientIds
                                    	GROUP_CONCAT(Client.name) as clientNames,
                                    	Ticket.userFirstName,
                                    	Ticket.userLastName,
                                    	auction_mstr.auction_wholesale as remitStatus,
                                    	#Track.applyToMembershipBal,
                                    	OfferType.offerTypeName,
                                    	Ticket.userCountry,
                                    	Ticket.userState,
                                    	Ticket.userCity,
                                    	Ticket.requestQueueDateTime,
                                    	Ticket.billingPrice,
                                    	TicketStatus.ticketStatusName,
                                    	SUM(PaymentDetail2.paymentAmount) as moneyCollected,
                                    	IF(SUM(PaymentDetail2.paymentAmount)>=Ticket.billingPrice, MAX(PaymentDetail2.ppResponseDate), '') AS dateCollected
                                FROM ticket AS Ticket
                                LEFT JOIN ticketStatus AS TicketStatus USING (ticketStatusId)
                                LEFT JOIN offerType as OfferType ON (OfferType.offerTypeId = Ticket.offerTypeId)
                                LEFT JOIN offer AS Offer ON (Offer.offerId = Ticket.offerId)
                                LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                                LEFT JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                                LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Ticket.packageId)
                                LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
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
	                $conditions[$k] =   $ca['field'].' IN('.implode(',', $values).')';
	            } else {
	                $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }
	            
	        endif; //end generate SQL for between condition
	    }
	    
	    $conditions[] = 'Ticket.formatId = 2';  //filter only fixed price types
	    return implode($conditions, ' AND ');
	}
	
	function aging() {
	        $sql = "SELECT Client.clientId, Client.name,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance,
            	                        DATEDIFF(NOW(), Loa.startDate) as age
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -91 DAY) AND NOW()
            					        AND Loa.membershipBalance > 0
                                GROUP BY Client.clientId, Loa.loaId
                                ORDER BY Loa.startDate DESC, Loa.membershipBalance DESC";

	        $results['0 to 90'] = $this->OfferType->query($sql);
	        
	        $sql = "SELECT Client.clientId, Client.name,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance,
            	                        DATEDIFF(NOW(), Loa.startDate) as age
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -181 DAY) AND DATE_ADD(NOW(), INTERVAL -91 DAY)
            					    AND Loa.membershipBalance > 0
                                GROUP BY Client.clientId, Loa.loaId
                                ORDER BY Loa.startDate DESC, Loa.membershipBalance DESC";

	        $results['91 to 180'] = $this->OfferType->query($sql);
	        
	        $sql = "SELECT Client.clientId, Client.name,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance,
            	                        DATEDIFF(NOW(), Loa.startDate) as age
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -271 DAY) AND DATE_ADD(NOW(), INTERVAL -181 DAY)
            					    AND Loa.membershipBalance > 0
                                GROUP BY Client.clientId, Loa.loaId
                                ORDER BY Loa.startDate DESC, Loa.membershipBalance DESC";

	        $results['181 to 270'] = $this->OfferType->query($sql);
	        
	        $sql = "SELECT Client.clientId, Client.name,
            	                        Loa.loaId,
            	                        Loa.startDate,
            	                        MAX(Loa.endDate) AS loaEndDate,
            	                        Loa.membershipFee, Loa.membershipBalance,
            	                        DATEDIFF(NOW(), Loa.startDate) as age
                                FROM client AS Client
                                INNER JOIN loa AS Loa ON (Loa.clientId = Client.clientId)
            					WHERE Loa.startDate BETWEEN DATE_ADD(NOW(), INTERVAL -1 YEAR) AND DATE_ADD(NOW(), INTERVAL -271 DAY)
            					    AND Loa.membershipBalance > 0
                                GROUP BY Client.clientId, Loa.loaId
                                ORDER BY Loa.startDate DESC, Loa.membershipBalance DESC";

	        $results['271 to 365'] = $this->OfferType->query($sql);

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
                            END as timeOfDay
                    FROM offer AS Offer
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingMasterId)
                    LEFT JOIN offerType AS OfferType ON (SchedulingMaster.offerTypeId = OfferType.offerTypeId)
                    WHERE $conditions
                    GROUP BY onlyEndDate, timeOfDay, OfferType.offerTypeId
                    ORDER BY onlyEndDate, timeOfDay ASC";

	        $results = $this->OfferType->query($sql);

	        //have to get the results in a format that we can easily loop through
	        $rows = array();
	        foreach ($results as $r) {
	            $rows[$r[0]['onlyEndDate']][$r['OfferType']['offerTypeName']][$r[0]['timeOfDay']] = $r[0]['numOffers'];
	        }
	        
            $this->set('data', $this->data);
	        $this->set('results', $rows);
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
                               LEFT JOIN paymentDetail AS PaymentDetail USING (ticketId)
                               LEFT JOIN paymentProcessor AS PaymentProcessor USING (paymentProcessorId)
                               LEFT JOIN userPaymentSetting AS UserPaymentSetting USING (userPaymentSettingId)
                               INNER JOIN package AS Package USING(packageId)
                               LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
                               INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                               INNER JOIN client as Client ON(Client.clientId = ClientLoaPackageRel.clientId)
                               LEFT JOIN track AS Track USING(trackId)
                        WHERE $conditions";

    	    $results = $this->OfferType->query($sql);
    	    $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / $this->perPage);
            
            $sql = "SELECT SchedulingInstance.endDate,
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
                           PaymentDetail.ccType,
                           PaymentDetail.ppCardNumLastFour,
                           PaymentDetail.ppExpMonth,
                           PaymentDetail.ppExpYear,
                           SUM(PaymentDetail.ppBillingAmount) as revenue,
                           Package.numNights,
                           OfferType.offerTypeName,
                           ROUND((SUM(PaymentDetail.ppBillingAmount) / Package.approvedRetailPrice * 100)) as percentOfRetail,
                           PaymentProcessor.paymentProcessorName,
                           auction_mstr.auction_wholesale as remitStatus,
                           #Track.applyToMembershipBal,
                           Package.validityStartDate,
                           Package.validityEndDate
                    FROM ticket AS Ticket
                           INNER JOIN offer AS Offer USING(offerId)
                           LEFT JOIN offerType AS OfferType ON (Ticket.offerTypeId = OfferType.offerTypeId)
                           LEFT JOIN schedulingInstance AS SchedulingInstance USING(schedulingInstanceId)
                           LEFT JOIN paymentDetail AS PaymentDetail ON (PaymentDetail.ticketId = Ticket.ticketId)
                           LEFT JOIN paymentProcessor AS PaymentProcessor USING (paymentProcessorId)
                           LEFT JOIN userPaymentSetting AS UserPaymentSetting ON (UserPaymentSetting.userPaymentSettingId = PaymentDetail.userPaymentSettingId)
                           LEFT JOIN package AS Package USING(packageId)
                           LEFT JOIN luxurymasterMigrate.auction_mstr as auction_mstr ON (auction_mstr.auction_id = Package.packageId)
                           LEFT JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Ticket.packageId)
                           LEFT JOIN client as Client ON(Client.clientId = ClientLoaPackageRel.clientId)
                           LEFT JOIN track AS Track USING(trackId)
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
	
	function cmr() {
	    if (!empty($this->data)) {
	        $conditions = $this->_build_conditions($this->data);
	        
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
                    $subquery = '(SELECT COUNT(*) FROM offerLive as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (1,2,6) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers > 0';
                    break;
                case 2:
                    $subquery = '(SELECT COUNT(*) FROM offerLive as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.offerTypeId IN (3,4) AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers > 0';
                    break;
                case 3:
                    //all
                    break;
                case 4:
                    $subquery = '(SELECT COUNT(*) FROM offerLive as OfferLive2 WHERE NOW() BETWEEN OfferLive2.startDate AND OfferLive2.endDate AND OfferLive2.clientId = Client.clientId) AS countSubqueryOffers,';
                    $having = 'HAVING countSubqueryOffers = 0';
                    break;
            endswitch;

            $sql = "SELECT COUNT(DISTINCT Loa.loaId) as numRecords
                        FROM client as Client
                        LEFT JOIN offerLive as OfferLive USING(clientId)
                        INNER JOIN loa as Loa USING(clientId)
                        LEFT JOIN loaLevel as LoaLevel USING(loaLevelId)
                        WHERE Loa.endDate >= NOW() AND $conditions";

    	    $results = $this->OfferType->query($sql);
    	    $numRecords = $results[0][0]['numRecords'];
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
                        DATEDIFF(NOW(), Loa.startDate) as loaNumberOfDaysActive,
                        ROUND( (Loa.loaValue / DATEDIFF(Loa.endDate, Loa.startDate)), 2) as dailyMembershipFee,
                        ROUND( (Loa.loaValue - Loa.membershipBalance) / (Loa.loaValue / DATEDIFF(Loa.endDate, Loa.startDate)) ) as numDaysPaid,
                        (Loa.startDate + INTERVAL ( (Loa.loaValue - Loa.membershipBalance) / (Loa.loaValue / DATEDIFF(Loa.endDate, Loa.startDate)) ) DAY) as paidThru,
                        DATEDIFF(Loa.endDate, (Loa.startDate + INTERVAL ( (Loa.loaValue - Loa.membershipBalance) / (Loa.loaValue / DATEDIFF(Loa.endDate, Loa.startDate)) ) DAY)) as daysBehindSchedule,
                        Client.managerUsername
                    FROM client as Client
                    INNER JOIN loa as Loa USING(clientId)
                    LEFT JOIN loaLevel as LoaLevel USING(loaLevelId)
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
	    if (!empty($this->data)) {
	        
	        if(isset($this->data['condition1'])) {
	            $liveDuringStartDate  = $this->data['condition1']['value']['between'][0];
	            $liveDuringEndDate    = $this->data['condition1']['value']['between'][1];
	            
	            $condition1Saved = $this->data['condition1'];
	            unset($this->data['condition1']);
	        }
	        
	        $conditions = $this->_build_conditions($this->data);
	        
	        
	        
	        if (isset($liveDuringStartDate)) {
	            $this->data['condition1'] = $condition1Saved; // restore this so the drop down reflects the right data
	            if (strlen($conditions)) {
	                $conditions .= " AND ";
	            }
	            
	            $conditions .= "(startDate BETWEEN '$liveDuringStartDate' AND '$liveDuringEndDate' + INTERVAL 1 DAY";
	            $conditions .= " OR endDate BETWEEN '$liveDuringStartDate' AND '$liveDuringEndDate' + INTERVAL 1 DAY";
	            $conditions .= " OR (startDate <= '$liveDuringStartDate' AND endDate >= '$liveDuringEndDate')";
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
	
	//TODO: A lot of duplication of code, use this method as a template for all the others and cut down on the number of times the following code is repeated
	function _build_conditions($data) {
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
    	                $conditions[$k] =   "{$field} LIKE '{$ca['value']}%'";
    	        } elseif (strpos($ca['value'], '!=') !== false) {
    	            $value = substr($ca['value'], strpos($ca['value'], '=')+2);
	                $conditions[$k] =   "{$ca['field']} != '{$value}'";
    	        } else {
	                $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'";
	            }
	            
	        endif; //end generate SQL for between condition
	    }
	    
	    return implode($conditions, ' AND ');
	}
}
?>