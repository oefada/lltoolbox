<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('OfferType');
	var $helpers = array('Pagination');
	//TODO: Add sorting, speed up the sql by adding indexes or a loading splash page, double check accuracy of data
	function offer_search() {
	    if (!empty($this->params['named']['filter'])) {
	        $filter = urldecode($this->params['named']['filter']);
	        $get    = @unserialize($filter);

	        if ($get !== false) {
	            $this->data = $get;
	        }
	    }

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
}
?>