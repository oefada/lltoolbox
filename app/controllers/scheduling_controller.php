<?php
class SchedulingController extends AppController {

	var $name = 'Scheduling';
	var $uses = array('Package', 'SchedulingMaster', 'SchedulingInstance');

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}
	
	function index($clientId = null)
	{
		/* Grab all necessary parameters from the URL */
		$clientId 	= @$this->params['named']['clientId'];
		$month 		= @$this->params['named']['month'];
		$month 		= (empty($month)) ? date('m') : $month;
		$year 		= @$this->params['named']['year'];
		$year		= (empty($year)) ? date('Y') : $year;
		
		// we need to know how many days are in this month
		$monthDays = $this->_monthDays($month, $year);

		$monthYearString = date('F Y', strtotime($year.'-'.$month.'-01'));
		
		$packages = $this->_clientPackages($clientId, $month);
		
		$this->set('packages', $packages);

		$this->set(compact('clientId', 'month', 'year', 'monthDays', 'monthYearString'));
	}
	
	/**
	 * Method returns all of the packages for a client id
	 *
	 * @param int $clientId
	 * @return array $packages an associative array with all of the package data
	 */
	function _clientPackages($clientId, $month) {
		$this->Package->ClientLoaPackageRel->recursive = 2;
		$this->Package->ClientLoaPackageRel->Behaviors->attach('Containable');
	    
		$this->Package->ClientLoaPackageRel->contain('Package');
		$packages = $this->Package->ClientLoaPackageRel->findAllByClientId($clientId);
	
		$this->_addPackageSchedulingInstances(&$packages, $month);
		
		return $packages;
	}
	
	function _addPackageSchedulingInstances(&$packages, $month = null) {
		foreach($packages as $package):
			$packageIds[] = $package['Package']['packageId'];
		endforeach;
		
		$this->Package->SchedulingMaster->recursive = 1;
		$this->Package->SchedulingMaster->Behaviors->attach('Containable');
		$this->Package->SchedulingMaster->contain('SchedulingInstance');
		$this->Package->SchedulingMaster->SchedulingInstance->conditions = array('OR' => array('month(SchedulingInstance.startDate)' => date('m'), 
						'month(SchedulingInstance.endDate)' => date('m')
				));
		
		$packageSchedulingInstance = $this->Package->SchedulingMaster->find('all', array('conditions' => 
																					array('SchedulingMaster.packageId' => $packageIds),
																					'contain' => array('SchedulingInstance' => array('conditions' => array('OR'=> array('month(SchedulingInstance.startDate) =' => $month, 
																									'month(SchedulingInstance.endDate) =' => $month))
																							))
																				   )
																			);
						
		foreach($packageSchedulingInstance as $instance) {
			$instances[$instance['SchedulingMaster']['packageId']][] = $instance;
		}
		
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