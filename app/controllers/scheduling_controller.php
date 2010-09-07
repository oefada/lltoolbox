<?php
class SchedulingController extends AppController {

	var $name = 'Scheduling';
	var $uses = array('Package', 'SchedulingMaster', 'SchedulingInstance', 'Ticket', 'Client');		//we need to access more than the default model in here

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
		$this->set('clientId', $this->params['named']['clientId']);
	}
	
	/**
	 * The main landing point for scheduling. All other methods in this class are  called from here.
	 * Method sets up the necessary variables needed to render the main scheduling interface.
	 *
	 * @param int $clientId the id of the client that we are scheduling for  
	 */
	function index($clientId = null)
	{
		/* Grab all necessary parameters from the URL */
		$clientId 	= @$this->params['named']['clientId'];						//client id is used to fetch all packages for this client
		$month 		= empty($this->params['named']['month']) ? date('m') : $this->params['named']['month'];
		$year 		= empty($this->params['named']['year']) ? date('Y') : $this->params['named']['year'];
		$monthDays 	= $this->_monthDays($month, $year);							// we need to know how many days are in this month

		$monthYearString = date('F Y', strtotime($year.'-'.$month.'-01'));		//string to display as a heading in the view
		
		$packages = $this->_clientPackages($clientId, $month, $year);					//grab all client packages
		
		$client = $this->Package->ClientLoaPackageRel->Client->find('first', array('contain' => array(), 'conditions' => array('Client.clientId' => $clientId)));
		$currentLoaId = $client['Client']['currentLoaId'];
		$currentLoa = $this->Package->ClientLoaPackageRel->Loa->find('first', array('contain' => array(), 'conditions' => array('Loa.loaId' => $currentLoaId)));

        $this->set('currentLoa', $currentLoa);
		$this->set('loaEndDate', $currentLoa['Loa']['endDate']);
		$client['Client'] = $client['Client'];								//the first package has the client details
		$clientName = $client['Client']['name'];
        $loaBalanceFlag = $this->_setLoaBalanceFlag($clientId, $currentLoa['Loa']);
        $this->set('packages', $packages);
		$this->set(compact('clientId', 'month', 'year', 'monthDays', 'monthYearString', 'clientName', 'client', 'loaBalanceFlag'));
	}

	function close_offers($clientId = null) {
		$clientId 	= @$this->params['named']['clientId'];		
		if (!$clientId || !is_numeric($clientId) || !($clientId > 0)) {
			die('INVALID OPERATION');
		}

		if (!empty($this->data)) {
			if (!isset($this->data['closeIt']) || !$this->data['clientId'] || ($clientId != $this->data['clientId'])) {
				die('INVALID OPERATION!  Something has gone wrong.  Please contact your local friendly developer.  Heard those tech guys are awesome.');
			}
			$this->Package->ClientLoaPackageRel->recursive = -1;
			$packages = $this->Package->ClientLoaPackageRel->findAllByclientId($clientId);
			$schedulingMasterIds = array();
			foreach ($packages as $p) {
				$schedulingMasterIds = array_merge($schedulingMasterIds, $this->Ticket->getSmIdsFromPackage($p['ClientLoaPackageRel']['packageId']));
			}
			$schedulingMasterIds = array_unique($schedulingMasterIds);

			if (!empty($schedulingMasterIds)) {
				if ($this->Ticket->__runTakeDown(implode(',', $schedulingMasterIds))) {
					$this->Session->setFlash(__('Scheduling blocks have been taken down.  Have a nice day. :)', true), 'default', array(), 'success');				
				} else {
					$this->Session->setFlash(__('There were no scheduling blocks to pull down. Have a nice day.', true), 'default', array(), 'error');				
				}
			} else {
				$this->Session->setFlash(__('There were no packages to take down.', true), 'default', array(), 'error');				
			}
			$this->set('closeModalbox', true);
		}

		$this->Client->recursive = -1;
		$client = $this->Client->read(null, $clientId);
		$this->set('client', $client);
		$this->set('currentTab', false);
		$this->set('searchController', false);
	}

	/**
	 * Method returns all of the packages for a client id
	 *
	 * @param int $clientId
	 * @return array $packages an associative array with all of the package data needed in the view
	 */
	function _clientPackages($clientId, $month, $year) {
		$this->Package->ClientLoaPackageRel->Behaviors->attach('Containable');

		$this->Package->ClientLoaPackageRel->contain('Package', 'Client', 'Loa');
		
		$numDaysInMonth = date('t', strtotime($year.'-'.$month.'-01'));
		$loa = $this->Package->query("SELECT GROUP_CONCAT(loaId) AS loaIds FROM loa AS Loa WHERE ('$year-$month-01' BETWEEN Loa.startDate AND Loa.endDate OR '$year-$month-$numDaysInMonth' BETWEEN Loa.startDate AND Loa.endDate) AND clientId = $clientId GROUP BY clientId");

        //if no current LOA is found, display all packages
        if (empty($loa)) {
            $packages = $this->Package->ClientLoaPackageRel->find('all', array('conditions' => array('ClientLoaPackageRel.clientId' => $clientId, 'packageStatusId' => 4)));
        } else { // if current LOA is found, display only packages for the current LOA to limit clutter
            $loaIds = $loa[0][0]['loaIds'];
            $loaIds = explode(',', $loaIds);
            $packages = $this->Package->ClientLoaPackageRel->find('all', array('conditions' => array('ClientLoaPackageRel.clientId' => $clientId, 'packageStatusId' => 4, 'ClientLoaPackageRel.loaId' => $loaIds)));
        }
        
	    $this->Package->SchedulingMaster->Behaviors->attach('Containable');
	    foreach ($packages as $k => $package) {
	        $packages[$k]['Package']['masterList'] = $this->Package->SchedulingMaster->find('all', array('conditions' => array('SchedulingMaster.packageId' => $package['Package']['packageId']),
	                                                                                        'fields' => array('SchedulingMaster.schedulingMasterId', 'SchedulingMaster.startDate', 'SchedulingMaster.endDate', 'OfferType.offerTypeName'),
	                                                                                        'contain' => array('OfferType')
	                                                                                        ));
	    }
	    
		$this->_addPackageSchedulingInstances($packages, $month, $year);
		
		return $packages;
	}
	/**
	 * Associates all of the scheduling instances with each package array
	 *
	 * @see _clientPackages()
	 * @param array $packages byref package array which we inject with scheduling instances
	 */
	function _addPackageSchedulingInstances(&$packages, $month = null, $year) {
		foreach($packages as $package):											//extract all the package ids
			$packageIds[] = $package['Package']['packageId'];
		endforeach;
		
		// We need to grab all of hte scheduling masters and instances associated to each package, but we want to contain it all
		$this->Package->SchedulingMaster->recursive = 1;
		$this->Package->SchedulingMaster->Behaviors->attach('Containable');
		$this->Package->SchedulingMaster->contain('SchedulingInstance');

		$containConditions = array('conditions' => array("(month(SchedulingInstance.startDate) = $month AND year(SchedulingInstance.startDate) = $year) OR
		                                                    (month(SchedulingInstance.endDate) =  $month AND year(SchedulingInstance.endDate) = $year) OR
		                                                    (SchedulingInstance.startDate <= '$year-$month-01' AND SchedulingInstance.endDate >= '$year-$month-".date('t', strtotime("$year-$month-01"))."')"));
		//select all instances for each package
		$packageSchedulingInstance = $this->Package->SchedulingMaster->find('all', array('conditions'   =>  array('SchedulingMaster.packageId'  =>  $packageIds),//array packageIds causes this to act as an IN clause
																					     'contain'      =>  array('SchedulingInstance'          =>  $containConditions,
                                                                                                                  'PricePoint')
																				        )
																			);

		//loop through all of the instances and associate them nicely so we can look them up below				
		foreach($packageSchedulingInstance as $instance) {
			$instances[$instance['SchedulingMaster']['packageId']][] = $instance;
		}
		
		//for each package we include a new associate array that has all of the scheduling details
		foreach($packages as &$package):
			$package['Scheduling'] = @$instances[$package['Package']['packageId']];
		endforeach;
	}
	
	function _setLoaBalanceFlag($clientId, $currentLoa) {
	    $loaMembershipBalance = $currentLoa['membershipBalance'];
	    $totalRevenue = $currentLoa['totalRevenue'];
	    
	    /* Get all of the instances that have not gone live yet for this client */
	    $totals = $this->Package->query("CALL getClientCurrentAndFutureInstances($clientId)");

	    if (empty($totals)) {
	        return array();
	    }

        $returnArray['totalOpeningBidSum']  =   $totals[0][0]['totalOpeningBidSum'];
        $returnArray['maxOpeningBid']       =   $totals[0][0]['maxOpeningBid'];
        $returnArray['class']               =   '';
        
        $returnArray['errorSchedulingInstanceId']   =     null;
	    if (isset($totals[0][0]['maxOpeningBid']) && $totals[0][0]['maxOpeningBid'] >= $loaMembershipBalance) {
	        $returnArray['class'] = 'icon-error';
	        
	        $result = $this->Package->query("SELECT schedulingInstanceId FROM clientLoaPackageRel AS ClientLoaPackageRel INNER JOIN
    	                                        schedulingMaster AS SchedulingMaster ON (ClientLoaPackageRel.packageId = SchedulingMaster.packageId) INNER JOIN
    	                                        schedulingInstance AS SchedulingInstance ON (SchedulingInstance.schedulingMasterId = SchedulingMaster.schedulingMasterId)
    	                                        WHERE ClientLoaPackageRel.clientId = $clientId AND SchedulingInstance.endDate > NOW() AND SchedulingMaster.openingBid = {$totals[0][0]['maxOpeningBid']}
    	                                        ORDER BY SchedulingInstance.endDate ASC
    	                                        LIMIT 1");
    	                                        
            $returnArray['errorSchedulingInstanceId'] = $result[0]['SchedulingInstance']['schedulingInstanceId'];
             return $returnArray;
	    } else if ($totals[0][0]['totalOpeningBidSum'] >= $loaMembershipBalance) {
	        $returnArray['class'] = 'icon-yellow';
	         return $returnArray;
	    }
	    
	    return array();
	}
	
	/**
	 * Method returns the number of days in a given month/year combo
	 *
	 * @param int|string $m the month
	 * @param int|string $y the year, only useful for calculating how many days in Feb (leap year)
	 * @return int the number of days in the month 
	 */
	function _monthDays($m, $y = null)
	{
		$y = (null === $y || empty($y)) ? date('Y') : $y;
		return date("t", strtotime($y . "-" . $m . "-01"));
	}
}
?>
