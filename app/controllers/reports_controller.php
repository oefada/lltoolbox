<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $uses = array('OfferType');
	//TODO: Add sorting, speed up the sql by adding indexes or a loading splash page, double check accuracy of data
	function offer_search() {
	    if (!empty($this->data)) {
	        $conditions = $this->_offer_search_build_conditions($this->data);
	        
	        $sql = "SELECT SchedulingInstance.schedulingInstanceId, Client.name, OfferType.offerTypeName, Package.packageName, Package.numNights, (SchedulingInstance.endDate >= NOW()) AS offerStatus,
	                IF(SchedulingInstance.endDate >= NOW(), SchedulingInstance.startDate, SchedulingInstance.endDate) AS dateOpenedOrClosed,
	                COUNT(Bid.bidId) as numberOfBids, Package.approvedRetailPrice, SchedulingMaster.openingBid, Loa.endDate, Loa.membershipBalance
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
	                GROUP BY Offer.offerId
	                ORDER BY Client.name
	                LIMIT 20";
	        $results = $this->OfferType->query($sql);

	        $this->set('results', $results);
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
	                continue;
	            }
	            
            /* If we got this far then that means we have adequate data for a where clause */
            if (is_array($betweenCondition)) {
                $firstValue = array_shift($betweenCondition);
                $secondValue = array_shift($betweenCondition);
                
                if (empty($firstValue) && empty($secondValue)) {
                    continue;
                }
            }
            
	        if (is_array($betweenCondition) && !empty($firstValue) && !empty($secondValue)) {
	            $conditions[$k] =   $ca['field'].' BETWEEN '."'{$firstValue}'".' AND '."'{$secondValue}'";
	        } elseif(is_array($betweenCondition) && !empty($firstValue)) {
	            $conditions[$k] =   $ca['field'].' = '."'{$firstValue}'";
	        } else {
	            if ($ca['field'] == 'Client.name' || $ca['field'] == 'Package.packageName') {
	                $ca['value'] = str_replace('*', '%', $ca['value']);
	                $conditions[$k] =   $ca['field'].' LIKE '."'{$ca['value']}'";
	            } else {
	               $conditions[$k] =   $ca['field'].' = '."'{$ca['value']}'"; 
	            }
	        }
	        
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