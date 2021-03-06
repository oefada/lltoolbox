<?php

class SchedulingController extends AppController {

	var $name = 'Scheduling';
	var $uses = array('Package', 'SchedulingMaster', 'SchedulingInstance', 'Ticket', 'Client');		//we need to access more than the default model in here
	var $helpers = array('Form');

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
		$cid=0;
		if (isset($this->params['named']['clientId']))$cid=$this->params['named']['clientId'];
		$this->set('clientId', $cid);
	}
	
	/**
	 * The main landing point for scheduling. All other methods in this class are  called from here.
	 * Method sets up the necessary variables needed to render the main scheduling interface.
	 *
	 * @param int $clientId the id of the client that we are scheduling for  
	 */
	function index($clientId = null)
	{

		//client id is used to fetch all packages for this client
		$clientId 	= @$this->params['named']['clientId'];						
		
		// User posts some data
		if(!empty($this->data) && isset($this->data['client']['notes'])) {			
			$this->data['client']['clientId'] = $clientId;
			$this->Package->ClientLoaPackageRel->Client->save($this->data['client'], array('callbacks' => false));
			$this->Session->setFlash(__('Changes have been saved', true));
		}
		
		/* Grab all necessary parameters from the URL */
		$month 		= empty($this->params['named']['month']) ? date('m') : $this->params['named']['month'];
		$year 		= empty($this->params['named']['year']) ? date('Y') : $this->params['named']['year'];
		// we need to know how many days are in this month
		$monthDays 	= $this->_monthDays($month, $year);							

		//string to display as a heading in the view
		$monthYearString = date('F Y', strtotime($year.'-'.$month.'-01'));		

		$packages = $this->_clientPackages($clientId, $month, $year);

        if (!empty($packages)){
            foreach ($packages as $k=>$v){
                $packages[$k]['Package']['isFamily']= $this->Package->isFamilyByPackageId($v['Package']['packageId']);

            }
        }

		$client = $this->Package->ClientLoaPackageRel->Client->find('first', 
			array(
			'contain' => array(), 
			'conditions' => array('Client.clientId' => $clientId)
			)
		);

		$currentLoaId = $client['Client']['currentLoaId'];
		$currentLoa = $this->Package->ClientLoaPackageRel->Loa->find('first', 
			array(
			'contain' => array(), 
			'conditions' => array('Loa.loaId' => $currentLoaId)
			)
		);

		$this->set('currentLoa', $currentLoa);
		$this->set('loaEndDate', $currentLoa['Loa']['endDate']);
		//the first package has the client details
		$client['Client'] = $client['Client'];								
		$clientName = $client['Client']['name'];
		$loaBalanceFlag = $this->_setLoaBalanceFlag($clientId, $currentLoa['Loa']);
		$this->set('packages', $packages);
		$this->set(compact('clientId', 'month', 'year', 'monthDays', 'monthYearString', 'clientName', 'client', 'loaBalanceFlag'));

	}

	function delPackageOffers(){

		$clientId=(int)$this->params['pass'][0];
		$packageId=(int)$this->params['pass'][1];

		if ($clientId<=0 || $packageId<=0){
			exit("Missing clientId or packageId. cid:$clientId|pid:$packageId|");
		}
	
		$schedulingMasterIds=$this->mngSMIDs($clientId,$packageId);

		$this->mngTakeDown($schedulingMasterIds);

		$this->redirect("/scheduling/index/clientId:".$clientId);

	}

	// have close_offers use this as well
	private function mngTakedown($schedulingMasterIds){

		if (!empty($schedulingMasterIds)) {
			if ($this->Ticket->__runTakeDown(implode(',', $schedulingMasterIds))) {
				$this->Session->setFlash(__('Scheduling blocks taken down.', true), 'default', array(), 'success');	
			} else {
				$this->Session->setFlash(__('No scheduling blocks to pull down.', true), 'default', array(), 'error');
			}
		}else{
			$this->Session->setFlash(__('No packages to take down.', true), 'default', array(), 'error');				
		}

	}

	private function mngSMIDs($clientId,$packageId=''){

		$packages = $this->Package->ClientLoaPackageRel->findAllByclientId($clientId);
		$schedulingMasterIds = array();
		foreach ($packages as $p) {
			// if packageId is empty, all offers for client will be taken down
			// if packageId is specified, only offers for that packageId will be taken down
			if ($packageId=='' || $packageId==$p['ClientLoaPackageRel']['packageId']){
				$arr=$this->Ticket->getSmIdsFromPackage($p['ClientLoaPackageRel']['packageId']);
				$schedulingMasterIds = array_merge($schedulingMasterIds, $arr);
			}
		}
		$schedulingMasterIds = array_unique($schedulingMasterIds);
		return $schedulingMasterIds;

	}

	function close_offers($clientId = null) {
		$clientId 	= @$this->params['named']['clientId'];		
		if (!$clientId || !is_numeric($clientId) || !($clientId > 0)) {
			die('INVALID OPERATION');
		}

		if (!empty($this->data)) {
			if (!isset($this->data['closeIt']) || !$this->data['clientId'] || ($clientId != $this->data['clientId'])) {
				die('Something has gone wrong.  Please contact your local friendly developer.');
			}
			$this->Package->ClientLoaPackageRel->recursive = -1;

			$schedulingMasterIds=$this->mngSMIDs($clientId);

			$this->mngTakeDown($schedulingMasterIds);

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

		$q="SELECT GROUP_CONCAT(loaId) AS loaIds FROM loa AS Loa WHERE ('$year-$month-01' BETWEEN Loa.startDate AND Loa.endDate OR '$year-$month-$numDaysInMonth' BETWEEN Loa.startDate AND Loa.endDate) AND clientId = $clientId GROUP BY clientId";
		$loa = $this->Package->query($q);

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
		  $tmp = $this->Package->SchedulingMaster->find('all', 
				array(
					'conditions' =>array('SchedulingMaster.packageId' => $package['Package']['packageId']),
					'fields' => array('SchedulingMaster.schedulingMasterId', 'SchedulingMaster.startDate', 'SchedulingMaster.endDate', 'OfferType.offerTypeName'),
					'contain' => array('OfferType')
				)
			);
			$packages[$k]['Package']['masterList']=$tmp;
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

		$containConditions = array(
			'conditions' => array("(month(SchedulingInstance.startDate) = $month AND year(SchedulingInstance.startDate) = $year) OR (month(SchedulingInstance.endDate) =  $month AND year(SchedulingInstance.endDate) = $year) OR (SchedulingInstance.startDate <= '$year-$month-01' AND SchedulingInstance.endDate >= '$year-$month-".date('t', strtotime("$year-$month-01"))."')")); 
		
		//select all instances for each package
		$packageSchedulingInstance = $this->Package->SchedulingMaster->find('all', 
			array(
				//array packageIds causes this to act as an IN clause
				'conditions' => array('SchedulingMaster.packageId'  =>  $packageIds),
				//'contain'	  =>  array('SchedulingInstance'		  =>  $containConditions, 'PricePoint')
				'contain'	  =>  array('PricePoint')
			)
		);

		$keepArr=array();
		foreach($packageSchedulingInstance as $i=>$arr){
			foreach($arr['SchedulingInstance'] as $j=>$row){

			// have php do this eval as the cake query was too slow
			//month(SchedulingInstance.startDate) = $month AND year(SchedulingInstance.startDate) = $year) 
			//OR (month(SchedulingInstance.endDate) =  $month AND year(SchedulingInstance.endDate) = $year) 
			// OR (SchedulingInstance.startDate <= '$year-$month-01' AND SchedulingInstance.endDate >= 
			// '$year-$month-".date('t', strtotime("$year-$month-01"))."')"

				$startDate=$row['startDate'];
				$startDate_month=date("m",strtotime($startDate));
				$startDate_year=date("Y",strtotime($startDate));
				$endDate=$row['endDate'];
				$endDate_month=date("m",strtotime($endDate));
				$endDate_year=date("Y",strtotime($endDate));
			
				if ( ($startDate_month==$month && $startDate_year==$year) 
					|| ($endDate_month==$month && $endDate_year==$year) 
					|| (strtotime($startDate)<=strtotime($year.'-'.$month.'-01'))
						&& strtotime($endDate)>=strtotime($year.'-'.$month.'-'.date('t', strtotime("$year-$month-01")))
					){
						$keepArr[$i][]=$j;	
				}	
			}
		}

		foreach($packageSchedulingInstance as $i=>$arr){
			foreach($arr['SchedulingInstance'] as $j=>$row){
				if (!isset($keepArr[$i]) || !is_array($keepArr[$i]) || !in_array($j,$keepArr[$i])){
					//echo "unset $j<br>";
					unset($packageSchedulingInstance[$i]['SchedulingInstance'][$j]);
				}
			}
		}

//printR($packageSchedulingInstance);exit;
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
		$returnArray['maxOpeningBid']	   =   $totals[0][0]['maxOpeningBid'];
		$returnArray['class']		       =   '';
		
		$returnArray['errorSchedulingInstanceId']   =	 null;
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
