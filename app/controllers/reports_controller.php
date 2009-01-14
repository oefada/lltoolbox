<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('OfferType');
	var $helpers = array('Pagination');
	//TODO: Add sorting, speed up the sql by adding indexes or a loading splash page, double check accuracy of data
	
	function beforeFilter() {
	    parent::beforeFilter();
	    if (!empty($this->params['named']['filter'])) {
	        $filter = urldecode($this->params['named']['filter']);
	        $get    = @unserialize($filter);

	        if ($get !== false) {
	            $this->data = $get;
	        }
	    }
	}
	function offer_search() {
	    if (!empty($this->data)) {
	        $conditions = $this->_offer_search_build_conditions($this->data);
	        
	        if (!empty($this->params['named']['page'])) {
	            $page = $this->params['named']['page'];
	            $limit = (($page-1)*20).',20';
	        } else {
	            $page = 1;
	            $limit = 20;
	        }
	        
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
	                INNER JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
	                INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
	                INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingInstanceId)
	                INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                INNER JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / 20);
                
	        $sql = "SELECT
	                SchedulingInstance.schedulingInstanceId, (SchedulingInstance.endDate >= NOW()) AS offerStatus, IF(SchedulingInstance.endDate >= NOW(), SchedulingInstance.startDate, SchedulingInstance.endDate) AS dateOpenedOrClosed,
	                Client.clientId, Client.name,
	                OfferType.offerTypeName,
	                Offer.offerId,
	                Package.packageId, Package.numNights, Package.approvedRetailPrice, 
	                COUNT(Bid.bidId) as numberOfBids,
	                SchedulingMaster.schedulingMasterId, SchedulingMaster.openingBid, SchedulingMaster.packageName,
	                Loa.loaId, Loa.endDate, Loa.membershipBalance,
	                (SELECT COUNT(*) 
	                    FROM schedulingInstance AS SchedulingInstance2
	                    INNER JOIN schedulingMaster AS SchedulingMaster2 
                        ON (SchedulingInstance2.schedulingMasterId = SchedulingMaster2.schedulingMasterId)
                        WHERE SchedulingMaster2.schedulingMasterId = SchedulingMaster.schedulingMasterId AND SchedulingInstance2.endDate >= NOW()
	                ) AS futureInstances
	                FROM offer AS Offer
	                INNER JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
	                INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
	                INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingInstanceId)
	                INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
	                INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
	                INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
	                INNER JOIN loa AS Loa ON (Loa.loaId = ClientLoaPackageRel.loaId)
	                INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
	                WHERE $conditions
	                GROUP BY Offer.offerId, SchedulingMaster.schedulingMasterId
	                ORDER BY $order
	                LIMIT $limit";
	        
	        $results = $this->OfferType->query($sql);
            
            $this->set('currentPage', $page);
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
	                $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	            } else {                                                //if only one value was entered, it's not a between
	                $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	            }
	            
	        else:
	            
	            //for Client.name and Package.packageName it's faster and better to do a match
	            if ($ca['field'] == 'Client.name' || $ca['field'] == 'Package.packageName') {
	                $conditions[$k] =   "MATCH({$ca['field']}) AGAINST('{$ca['value']}' IN BOOLEAN MODE)";
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
	    return implode($conditions, ' AND ');
	}
	
	function bids() {
	    if (!empty($this->data)) {
	        $conditions = $this->_bids_build_conditions($this->data);
	        
	        if (!empty($this->params['named']['page'])) {
	            $page = $this->params['named']['page'];
	            $limit = (($page-1)*20).',20';
	        } else {
	            $page = 1;
	            $limit = 20;
	        }
	        
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
	        
            $count = "SELECT COUNT(DISTINCT Offer.offerId) as numRecords
                    FROM offer AS Offer
                    LEFT JOIN ticket AS Ticket ON (Ticket.offerId = Offer.offerId)
                    LEFT JOIN ticket AS Ticket2 ON (Ticket2.offerId = Offer.offerId AND Ticket2.ticketStatusId IN (5,6))
                    LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingInstanceId)
                    INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                    INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Package.packageId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                    LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
                    WHERE $conditions";

	        $results = $this->OfferType->query($count);
	        $numRecords = $results[0][0]['numRecords'];
            $numPages = ceil($numRecords / 20);
                
	        $sql = "SELECT
                            Offer.offerId,
                        	Client.name,
                        	Track.applyToMembershipBal,
                        	OfferType.offerTypeName,
            				(SELECT Country.countryName FROM country AS Country WHERE Country.countryId = Client.countryId) AS country,
            				(SELECT State.stateName FROM state AS State WHERE State.stateId = Client.stateId) AS state,
                        	(SELECT City.cityName FROM city AS City WHERE City.cityId = Client.cityId) AS city,
                            (SchedulingMaster.openingBid / Package.approvedRetailPrice * 100) AS percentMinBid,
                            (Ticket.billingPrice / Package.approvedRetailPrice * 100) AS percentClose,
                        	Package.approvedRetailPrice,
                        	Package.numNights,
                        	SchedulingInstance.endDate,
                        	COUNT(Bid.bidId) AS numBids,
                        	COUNT(DISTINCT Bid.userId) AS uniqueBids,
                        	COUNT(DISTINCT Ticket.ticketId) AS numTickets,
                        	SUM(Ticket.billingPrice) AS moneyPotential,
                        	COUNT(DISTINCT Ticket2.ticketId) AS numTicketsCollected,
                        	SUM(Ticket2.billingPrice) AS moneyCollected
                    FROM offer AS Offer
                    LEFT JOIN ticket AS Ticket ON (Ticket.offerId = Offer.offerId)
                    LEFT JOIN ticket AS Ticket2 ON (Ticket2.offerId = Offer.offerId AND Ticket2.ticketStatusId = 6)
                    LEFT JOIN bid AS Bid ON (Bid.offerId = Offer.offerId)
                    INNER JOIN schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingInstanceId = Offer.schedulingInstanceId)
                    INNER JOIN schedulingMaster AS SchedulingMaster ON (SchedulingMaster.schedulingMasterId = SchedulingInstance.schedulingInstanceId)
                    INNER JOIN offerType as OfferType ON (OfferType.offerTypeId = SchedulingMaster.offerTypeId)
                    INNER JOIN package AS Package ON (Package.packageId = SchedulingMaster.packageId)
                    INNER JOIN clientLoaPackageRel AS ClientLoaPackageRel ON (ClientLoaPackageRel.packageId = Offer.packageId)
                    INNER JOIN client AS Client ON (Client.clientId = ClientLoaPackageRel.clientId)
                    LEFT JOIN track AS Track ON (Track.trackId = ClientLoaPackageRel.trackId)
                    WHERE $conditions
                    GROUP BY Client.clientId, Offer.offerId
                    ORDER BY $order
	                LIMIT $limit";
	        
	        $results = $this->OfferType->query($sql);
            
            $this->set('currentPage', $page);
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
}
?>