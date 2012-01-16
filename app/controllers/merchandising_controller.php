<?php
class MerchandisingController extends AppController
{
	var $name = 'Merchandising';
	var $uses = array('MerchDataGroup', 'MerchDataType', 'MerchDataEntries', 'Client');

	function __construct()
	{
		parent::__construct();
		$this->loadModel('MerchDataType');
		$this->loadModel('MerchDataEntries');
		
		// get tab header info
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('isHeader' => 1, 'merchDataTypeName' => 'Homepage Tabs')
		);
		$tabHeader = $this->MerchDataEntries->find('first', $params);
		
		if (is_array($tabHeader) && count($tabHeader) > 0) {
			$this->set('tabs', $tabHeader['MerchDataEntries']['merchDataArr']);
		}
		
	}

	function index()
	{

	}
	
	// ajax to get client info from link
	function clientInfo()
	{
		if (isset($_GET['linkUrl']) && !empty($_GET['linkUrl'])) {
			$clientArr = $this->setLinkUrl($_GET['linkUrl']);
			if (is_array($clientArr)
				&& isset($clientArr['Client'])
				&& isset($clientArr['Client']['clientId'])
			) {
				$client = $this->getClient($clientArr['Client']['clientId']);
				$client[0]['Client']['linkUrl'] = $_GET['linkUrl'];
				echo json_encode($client[0]['Client']);
			} else {
				echo json_encode(Array('linkUrl' => $_GET['linkUrl']));
			}
		} else {
			echo '[]';
		}
		die();
	}
	
	function billboard()
	{	
		// get merchDataType record for billboard
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('merchDataTypeName' => 'Billboard')
		);
		$billboardDataType = $this->MerchDataType->find('first', $params);
		
		// get welcome slide info
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('isHeader' => 1, 'merchDataTypeName' => 'Billboard')
		);
		$welcomeSlide = $this->MerchDataEntries->find('first', $params);

		// check form submit
		if (isset($_POST) && count($_POST) > 0) {
			if (isset($_POST['welcomeSlide'])) {
			
				// welcome slide
				unset($_POST['welcomeSlide']);
				
				if (isset($_POST['linkUrl'])) {
					$clientArr = $this->setLinkUrl($_POST['linkUrl']);
					if (is_array($clientArr) 
						&& isset($clientArr['Client'])
						&& isset($clientArr['Client']['clientId'])
					) {
						$_POST['clientId'] = $clientArr['Client']['clientId'];
					}
				}
				
				$jsonData = json_encode($_POST);
				$dataArr = Array(
					'isHeader' => 1,
					'merchDataTypeId' => $billboardDataType['MerchDataType']['id'],
					'merchDataJSON' => $jsonData
				);
				
				if (!$welcomeSlide) {
					// insert
					$this->MerchDataEntries->create();
					$this->MerchDataEntries->save($dataArr);
				} else {
					// update
					$this->MerchDataEntries->read(null, $welcomeSlide['MerchDataEntries']['id']);
					$this->MerchDataEntries->set($dataArr);
					$this->MerchDataEntries->save();
				}

				// get welcome slide info
				$params = Array(
					'recursive' => 0,
					'conditions' => Array('isHeader' => 1, 'merchDataTypeName' => 'Billboard')
				);
				$welcomeSlide = $this->MerchDataEntries->find('first', $params);

			} else if (isset($_POST['schedule-date'])) {
			
				// lookup data entries for date
				$params = Array(
					'recursive' => 0,
					'conditions' => Array('isHeader' => 0, 'startDate' => $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'], 'merchDataTypeName' => 'Billboard')
				);
				$currData = $this->MerchDataEntries->find('first', $params);

				$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
				$this->set('month', $_POST['month']);
				$this->set('day', $_POST['day']);
				$this->set('year', $_POST['year']);
				$this->set('scheduleDate', $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
				
			} else if (isset($_POST['slides'])) {
			
				// slides
				if (isset($_POST['imageUrl']) && is_array($_POST['imageUrl'])) {
					$params = Array(
						'recursive' => 0,
						'conditions' => Array('isHeader' => 0, 'startDate' => $_POST['scheduleDate'], 'merchDataTypeName' => 'Billboard')
					);
					$currData = $this->MerchDataEntries->find('first', $params);
					
					$slides = Array();
					foreach ($_POST['imageUrl'] AS $k => $imageUrl) {
						$slide = Array();
						$clientArr = $this->setLinkUrl($_POST['linkUrl'][$k]);
						$slide = Array(
							'imageUrl' => $imageUrl,
							'linkUrl' => $_POST['linkUrl'][$k],
							'linkText' => $_POST['linkText'][$k],
							'imageAlt' => $_POST['imageAlt'][$k],
							'headline' => $_POST['headline'][$k],
							'description' => $_POST['description'][$k]
						);
						if (is_array($clientArr)
							&& isset($clientArr['Client'])
							&& isset($clientArr['Client']['clientId'])
						) {
							$slide['clientId'] = $clientArr['Client']['clientId'];
						}
						
						$slides[] = $slide;
					}
					
					$jsonData = json_encode($slides);
					$dataArr = Array(
						'merchDataTypeId' => $billboardDataType['MerchDataType']['id'],
						'startDate' => $_POST['scheduleDate'],
						'merchDataJSON' => $jsonData,
					);
					// insert or update
					if (!$currData) {
						// insert
						$this->MerchDataEntries->create();
						$this->MerchDataEntries->save($dataArr);
					} else {
						// update
						$this->MerchDataEntries->read(null, $currData['MerchDataEntries']['id']);
						$this->MerchDataEntries->set($dataArr);
						$this->MerchDataEntries->save();
					}
					
					$params = Array(
						'recursive' => 0,
						'conditions' => Array('isHeader' => 0, 'startDate' => $_POST['scheduleDate'], 'merchDataTypeName' => 'Billboard')
					);
					$currData = $this->MerchDataEntries->find('all', $params);

					$this->set('currData', $currData[0]['MerchDataEntries']['merchDataArr']);
					$this->set('scheduleDate', $_POST['scheduleDate']);
					$this->set('dataSaved', true);
				}
				
			}

		}
		
		$this->set('welcomeSlide', $welcomeSlide);
		$this->set('welcomeSlideData', $welcomeSlide['MerchDataEntries']['merchDataArr']);
	
	}
	
	function tabs()
	{
	
	}
	
	function hometabs()
	{
		// select header row
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('merchDataTypeName' => 'Homepage Tabs')
		);
		$homepageTabsDataType = $this->MerchDataType->find('first', $params);
		
		// get tab header info
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('isHeader' => 1, 'merchDataTypeName' => 'Homepage Tabs')
		);
		$tabHeader = $this->MerchDataEntries->find('first', $params);
		if (is_array($tabHeader) && count($tabHeader) > 0) {
			$this->set('header', $tabHeader['MerchDataEntries']['merchDataArr']);
			$this->set('currData', $tabHeader['MerchDataEntries']['merchDataArr']);
		}
		
		// check form submits
		if (isset($_POST)) {
		
			if (isset($_POST['hometabs']) && $_POST['hometabs']) {
				
				if (isset($_POST['tabName']) && is_array($_POST['tabName'])) {
					$tabData = Array();

					foreach ($_POST['tabName'] AS $i => $tabName) {
						// use tabName as index for easy lookup
						$tabName = trim($tabName);
						$tabData[$tabName] = Array(
							'tabName' => $tabName,
							'algorithm' => $_POST['algorithm'][$i],
							'footerLink' => $_POST['footerLink'][$i],
							'footerText' => $_POST['footerText'][$i],
							'inactive' => isset($_POST['inactive-'.($i+1)]) ? 1 : 0,
							'merchDataGroupId' => $_POST['merchDataGroupId'][$i]
						);
					}

					if (count($tabData) > 0) {
						// merchDataGroup for each tab
						foreach ($tabData AS &$tab) {
							if (!isset($tab['merchDataGroupId']) || empty($tab['merchDataGroupId'])) {
								// insert
								$this->MerchDataGroup->create();
								$this->MerchDataGroup->save(Array('merchDataGroupName' => $tab['tabName']));
								$tab['merchDataGroupId'] = $this->MerchDataGroup->getLastInsertId();
							} else {
								// update
								$this->MerchDataGroup->read(null, $tab['merchDataGroupId']);
								$this->MerchDataGroup->set(Array('merchDataGroupName' => $tab['tabName']));
								$this->MerchDataGroup->save();
							}
						}
					
						$tabDataJSON = json_encode($tabData);
						$dataArr = Array(
							'isHeader' => 1,
							'merchDataTypeId' => $homepageTabsDataType['MerchDataType']['id'],
							'merchDataJSON' => $tabDataJSON
						);
						
						// insert or update
						if (!$tabHeader) {
							// insert
							$this->MerchDataEntries->create();
							$this->MerchDataEntries->save($dataArr);
						} else {
							// update
							$this->MerchDataEntries->read(null, $tabHeader['MerchDataEntries']['id']);
							$this->MerchDataEntries->set($dataArr);
							$this->MerchDataEntries->save();
						}
						
						$params = Array(
							'recursive' => 0,
							'conditions' => Array('isHeader' => 1, 'merchDataTypeName' => 'Homepage Tabs')
						);
						$currData = $this->MerchDataEntries->find('all', $params);
						$this->set('currData', $currData[0]['MerchDataEntries']['merchDataArr']);
						$this->set('tabs', $currData[0]['MerchDataEntries']['merchDataArr']);
						$this->set('dataSaved', true);
					}
				}
				
			}

		}
		
		if (isset($_GET['t'])) {
			$tabs = $tabHeader['MerchDataEntries']['merchDataArr'];
			$tabName = $_GET['t'];
			if (isset($tabs[$tabName])) {
				// get merchDataGroup row
				$params = Array(
					'conditions' => Array(
					'merchDataGroupName' => $tabName
					)
				);
				$merchDataGroup = $this->MerchDataGroup->find('first', $params);
			
				// check date submit
				if (isset($_POST['schedule-date']) && $_POST['schedule-date']) {
					$this->set('month', $_POST['month']);
					$this->set('day', $_POST['day']);
					$this->set('year', $_POST['year']);
					$this->set('scheduleDate', $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
					
					// get data for date
					$params = Array(
						'recursive' => 0,
						'conditions' => Array(
							'MerchDataEntries.merchDataGroupId' => $merchDataGroup['MerchDataGroup']['id'],
							'startDate' => $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']
						)
					);
					$currData = $this->MerchDataEntries->find('first', $params);
					
					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
				}
				
				if (isset($_POST['tab-data']) && $_POST['tab-data'] && isset($_POST['schedule-date'])) {
					// save tab  data
					$tabData = Array();
					foreach ($_POST['clientUrl'] AS $i => $clientUrl) {
					
						$tab = Array(
							'clientUrl' => $clientUrl,
							'packageId' => $_POST['packageId'][$i]
						);
						
						// lookup client url
						$clientArr = $this->setLinkUrl($clientUrl);
						if (is_array($clientArr)
							&& isset($clientArr['Client'])
							&& isset($clientArr['Client']['clientId'])
						) {
							$tab['clientId'] = $clientArr['Client']['clientId'];
							$tab['clientUrl'] = $clientUrl;
						}
						
						$tabData[] = $tab;
					}
						
					$merchDataJSON = json_encode($tabData);
					$dataArr = Array(
						'merchDataTypeId' => $homepageTabsDataType['MerchDataType']['id'],
						'merchDataGroupId' => $merchDataGroup['MerchDataGroup']['id'],
						'startDate' => $_POST['schedule-date'],
						'merchDataJSON' => $merchDataJSON
					);
					
					// insert or update
					if (!$currData) {
						// insert
						$this->MerchDataEntries->create();
						$this->MerchDataEntries->save($dataArr);
					} else {
						// update
						$this->MerchDataEntries->read(null, $currData['MerchDataEntries']['id']);
						$this->MerchDataEntries->set($dataArr);
						$this->MerchDataEntries->save();
					}
					
					// get data current data
					$params = Array(
						'recursive' => 0,
						'conditions' => Array(
							'MerchDataEntries.merchDataGroupId' => $merchDataGroup['MerchDataGroup']['id'],
							'startDate' => $_POST['schedule-date']
						)
					);
					$currData = $this->MerchDataEntries->find('first', $params);
					
					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
					$this->set('dataSaved', true);
				}

				$this->set('tabName', $tabName);
			} else {
				die('no data found for tab ' . $tabName);
			}
			
			$this->render('tab');
		}
		
	}
	
	
	public function inspiration()
	{
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('merchDataTypeName' => 'Inspiration')
		);
		$inspirationDataType = $this->MerchDataType->find('first', $params);
			
		if (isset($_POST['schedule-date'])) {
			$this->set('month', $_POST['month']);
			$this->set('day', $_POST['day']);
			$this->set('year', $_POST['year']);
			$this->set('scheduleDate', $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
			
			// get current data
			$params = Array(
				'recursive' => 0,
				'conditions' => Array(
						'merchDataTypeId' => $inspirationDataType['MerchDataType']['id'], 
						'startDate' => $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'])
			);
			$currData = $this->MerchDataEntries->find('first', $params);
			
			$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
			
			if (isset($_POST['inspiration']) && $_POST['inspiration']) {
				$inspArr = Array(
					'title' => $_POST['title'],
					'imageUrl' => $_POST['imageUrl'],
					'linkUrl' => $_POST['linkUrl'],
					'linkText' => $_POST['linkText']
				);
				
				if (isset($_POST['showcaseUrl']) && is_array($_POST['showcaseUrl'])) {
					$urlArr = Array();
					//var_dump($_POST['showcaseUrl']); die();
					foreach ($_POST['showcaseUrl'] AS $url) {
						$cUrl = $url;
						$clientArr = $this->setLinkUrl($cUrl);
						if (is_array($clientArr)
							&& isset($clientArr['Client'])
							&& isset($clientArr['Client']['clientId'])
						) {
							$urlArr[] = Array(
								'clientId' => $clientArr['Client']['clientId'],
								'linkUrl' => $cUrl
							);
						}
					}
					
					if (count($urlArr) > 0) {
						$inspArr['clients'] = $urlArr;
					}
					
					$dataArr = Array(
						'merchDataTypeId' => $inspirationDataType['MerchDataType']['id'],
						'startDate' => $_POST['schedule-date'],
						'merchDataJSON' => json_encode($inspArr)
					);
					
					// insert or update
					if (!$currData) {
						// insert
						$this->MerchDataEntries->create();
						$this->MerchDataEntries->save($dataArr);
					} else {
						// update
						$this->MerchDataEntries->read(null, $currData['MerchDataEntries']['id']);
						$this->MerchDataEntries->set($dataArr);
						$this->MerchDataEntries->save();
					}
					
					// get current data
					$params = Array(
						'recursive' => 0,
						'conditions' => Array('merchDataTypeId' => $inspirationDataType['MerchDataType']['id'], 'startDate' => $_POST['schedule-date'])
					);
					$currData = $this->MerchDataEntries->find('first', $params);
					
					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
					$this->set('dataSaved', true);
				}
				
			}
		}	
	}
	
	public function fauction()
	{
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('merchDataTypeName' => 'Featured Auction')
		);
		$fauctionDataType = $this->MerchDataType->find('first', $params);
			
		if (isset($_POST['schedule-date'])) {
			$this->set('month', $_POST['month']);
			$this->set('day', $_POST['day']);
			$this->set('year', $_POST['year']);
			$this->set('scheduleDate', $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']);
			
			// get current data
			$params = Array(
				'recursive' => 0,
				'conditions' => Array(
						'merchDataTypeId' => $fauctionDataType['MerchDataType']['id'], 
						'startDate' => $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'])
			);
			$currData = $this->MerchDataEntries->find('first', $params);
			
			$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
			
			if (isset($_POST['fauction']) && $_POST['fauction']) {
			
				if (isset($_POST['clientUrl']) && is_array($_POST['clientUrl'])) {
					$urlArr = Array();

					foreach ($_POST['clientUrl'] AS $url) {
						$cUrl = $url;
						$clientArr = $this->setLinkUrl($cUrl);
						if (is_array($clientArr)
							&& isset($clientArr['Client'])
							&& isset($clientArr['Client']['clientId'])
						) {
							$urlArr[] = Array(
								'clientId' => $clientArr['Client']['clientId'],
								'linkUrl' => $cUrl
							);
						}
					}
					
					$arr = Array(
						'clients' => $urlArr
					);
					
					$dataArr = Array(
						'merchDataTypeId' => $fauctionDataType['MerchDataType']['id'],
						'startDate' => $_POST['schedule-date'],
						'merchDataJSON' => json_encode($arr)
					);
					
					// insert or update
					if (!$currData) {
						// insert
						$this->MerchDataEntries->create();
						$this->MerchDataEntries->save($dataArr);
					} else {
						// update
						$this->MerchDataEntries->read(null, $currData['MerchDataEntries']['id']);
						$this->MerchDataEntries->set($dataArr);
						$this->MerchDataEntries->save();
					}
					
					// get current data
					$params = Array(
						'recursive' => 0,
						'conditions' => Array('merchDataTypeId' => $fauctionDataType['MerchDataType']['id'], 'startDate' => $_POST['schedule-date'])
					);
					$currData = $this->MerchDataEntries->find('first', $params);
					
					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
					$this->set('dataSaved', true);
				}
				
			}
		}
		
	}
	
	
	
	private function getClient($clientId)
	{
		$params = Array(
			'recursive' => 0,
			'conditions' => Array(
				'clientId' => $clientId
			)
		);
		return $this->Client->find('all', $params);
	}
	
	// alters linkUrl and tries returns client info if found from seo-name
	private function setLinkUrl(&$linkUrl)
	{
		// make link urls relative
		if (!empty($linkUrl)) {
			if ( ($urlStr = str_replace('http://www.luxurylink.com/', '/', $linkUrl))
				|| ($urlStr = str_replace('www.luxurylink.com/', '/', $linkUrl))
			) {
				// try lookup of client info
				if (substr($urlStr, 0, 10) == '/fivestar/') {
					$linkUrl = $urlStr;
					return $this->Client->getClientBySeoUrl(str_replace('/fivestar/', '', $urlStr));
				}
			}
		}
	}
	
}
