<?php
class SchedulingController extends AppController {

	var $name = 'Scheduling';
	var $uses = array('Package', 'SchedulingMaster', 'SchedulingInstance');		//we need to access more than the default model in here

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
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
		
		$currentLoaId = $packages[0]['Client']['currentLoaId'];
		$currentLoa = $this->Package->ClientLoaPackageRel->Loa->find('first', array('contain' => array(), 'conditions' => array('Loa.loaId' => $currentLoaId), 'fields' => 'Loa.endDate'));

		$this->set('loaEndDate', $currentLoa['Loa']['endDate']);
		
		$client['Client'] = $packages[0]['Client'];								//the first package has the client details
		$clientName = $packages[0]['Client']['name'];

        $this->set('packages', $packages);
		$this->set(compact('clientId', 'month', 'year', 'monthDays', 'monthYearString', 'clientName', 'client'));
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
		$packages = $this->Package->ClientLoaPackageRel->find('all', array('conditions' => array('ClientLoaPackageRel.clientId' => $clientId, 'packageStatusId' => 4)));
	
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
		
		$containConditions = array('conditions' => array('OR'=> array('month(SchedulingInstance.startDate) =' =>  $month, 
					                                                  'month(SchedulingInstance.endDate) ='   =>  $month
					                                                  ),
					                                     'AND' => array('OR'=> array('year(SchedulingInstance.startDate) =' =>  $year, 
                                                                      'year(SchedulingInstance.endDate) ='   =>  $year
                                                                      ))
                                                         )
					              );
		//select all instances for each package
		$packageSchedulingInstance = $this->Package->SchedulingMaster->find('all', array('conditions'   =>  array('SchedulingMaster.packageId'  =>  $packageIds),//array packageIds causes this to act as an IN clause
																					     'contain'      =>  array('SchedulingInstance'          =>  $containConditions)
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