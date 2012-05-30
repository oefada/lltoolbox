<?php
class MerchandisingController extends AppController
{
	var $name = 'Merchandising';
	var $uses = array(
		'MerchDataGroup',
		'MerchDataType',
		'MerchDataEntries',
		'Client'
	);

	function __construct()
	{
		parent::__construct();
		$this->loadModel('MerchDataType');
		$this->loadModel('MerchDataEntries');

		// get tab header info
		$params = Array(
			'recursive' => 0,
			'conditions' => Array(
				'isHeader' => 1,
				'merchDataTypeName' => 'Homepage Tabs'
			)
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
			if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
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
			'conditions' => Array(
				'isHeader' => 1,
				'merchDataTypeName' => 'Billboard'
			)
		);
		$welcomeSlide = $this->MerchDataEntries->find('first', $params);

		$others = $this->getOtherScheduled($billboardDataType['MerchDataType']['id']);
		$this->set('others', $others);

		// check form submit
		if (isset($_POST) && count($_POST) > 0) {
			if (isset($_POST['welcomeSlide'])) {

				// welcome slide
				unset($_POST['welcomeSlide']);

				if (isset($_POST['linkUrl'])) {
					$clientArr = $this->setLinkUrl($_POST['linkUrl']);
					if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
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
					'conditions' => Array(
						'isHeader' => 1,
						'merchDataTypeName' => 'Billboard'
					)
				);
				$welcomeSlide = $this->MerchDataEntries->find('first', $params);

			} else if (isset($_POST['data']['scheduleDate']) && !isset($_POST['slides'])) {
				// lookup data entries for date
				$params = Array(
					'recursive' => 0,
					'conditions' => Array(
						'isHeader' => 0,
						'startDate' => $_POST['data']['scheduleDate'],
						'merchDataTypeName' => 'Billboard'
					)
				);
				$currData = $this->MerchDataEntries->find('first', $params);

				if (!$currData && isset($others['current']['merchDataArr'])) {
					$this->set('currData', $others['current']['merchDataArr']);
				} else {
					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
				}
				$this->set('scheduleDate', $_POST['data']['scheduleDate']);

			} else if (isset($_POST['slides'])) {

				// slides
				if (isset($_POST['imageUrl']) && is_array($_POST['imageUrl'])) {
					$params = Array(
						'recursive' => 0,
						'conditions' => Array(
							'isHeader' => 0,
							'startDate' => $_POST['data']['scheduleDate'],
							'merchDataTypeName' => 'Billboard'
						)
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
						$slide['linkUrl']=preg_replace('/^http:\/\/www\.luxurylink\.com\//','/',$slide['linkUrl']);
						$slide['imageUrl'] = $this->_filterImageUrl($slide['imageUrl']);
						if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
							$slide['clientId'] = $clientArr['Client']['clientId'];
						}

						$slides[] = $slide;
					}

					$jsonData = json_encode($slides);
					$dataArr = Array(
						'merchDataTypeId' => $billboardDataType['MerchDataType']['id'],
						'startDate' => $_POST['data']['scheduleDate'],
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
						'conditions' => Array(
							'isHeader' => 0,
							'startDate' => $_POST['data']['scheduleDate'],
							'merchDataTypeName' => 'Billboard'
						)
					);
					$currData = $this->MerchDataEntries->find('all', $params);

					$this->set('currData', $currData[0]['MerchDataEntries']['merchDataArr']);
					$this->set('scheduleDate', $_POST['data']['scheduleDate']);
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
			'conditions' => Array(
				'isHeader' => 1,
				'merchDataTypeName' => 'Homepage Tabs'
			)
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
							'inactive' => isset($_POST['inactive-' . ($i + 1)]) ? 1 : 0,
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
								// if none read, create
								if (!is_array($this->MerchDataGroup->data)) {
									$this->MerchDataGroup->create();
									$this->MerchDataGroup->save(Array(
										'merchDataGroupName' => $tab['tabName'],
										'id' => $tab['merchDataGroupId']
									));
								} else {
									$this->MerchDataGroup->set(Array('merchDataGroupName' => $tab['tabName']));
									$this->MerchDataGroup->save();
								}
							}
						}

						// lltEvent
						foreach ($tabData as $xTabKey => &$xTabValue) {
							$xlltEventId = $xTabValue['merchDataGroupId'];
							$xlltTabName = strtolower(strip_tags(str_replace('<', ' <', $xTabValue['tabName'])));
							$xlltTabName = preg_replace('/[^A-Za-z0-9]/', '-', $xlltTabName);
							$xlltTabName = preg_replace('/\-+/', '-', $xlltTabName);
							$xTabPrefix = 'client-click home-page ' . $xlltEventId . ' ';
							$xTabName = $xTabPrefix . $xlltTabName;
							$xEventQuery = $this->MerchDataGroup->query('SELECT lltEventId FROM lltEvent WHERE lltEventGroupId=12 AND eventName LIKE ?', array($xTabPrefix . '%'));
							$xlltEventId = NULL;
							if (count($xEventQuery) > 0) {
								$xlltEventId = $xEventQuery[0]['lltEvent']['lltEventId'];
							}
							$this->MerchDataGroup->query('REPLACE INTO lltEvent (lltEventId,lltEventGroupId,eventName) VALUES (?,12,?)', array(
								$xlltEventId,
								$xTabName
							));
							$xEventQuery = $this->MerchDataGroup->query('SELECT * FROM lltEvent WHERE lltEventGroupId=12 AND eventName LIKE ?', array($xTabPrefix . '%'));
							$xlltEventId = $xEventQuery[0]['lltEvent']['lltEventId'];
							$xTabValue['lltgEventId'] = $xlltEventId;
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
							'conditions' => Array(
								'isHeader' => 1,
								'merchDataTypeName' => 'Homepage Tabs'
							)
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
				$others = $this->getOtherScheduled($homepageTabsDataType['MerchDataType']['id'], $tabs[$tabName]['merchDataGroupId']);
				$this->set('others', $others);

				// check date submit
				if (isset($_POST['data']['scheduleDate']) && $_POST['data']['scheduleDate']) {
					$this->set('scheduleDate', $_POST['data']['scheduleDate']);

					// get data for date
					$params = Array(
						'recursive' => 0,
						'conditions' => Array(
							'MerchDataEntries.merchDataGroupId' => $tabs[$tabName]['merchDataGroupId'],
							'startDate' => $_POST['data']['scheduleDate']
						)
					);
					$currData = $this->MerchDataEntries->find('first', $params);

					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
				}

				if (isset($_POST['tab-data']) && $_POST['tab-data'] && isset($_POST['data']['scheduleDate'])) {
					// save tab data
					$tabData = Array();
					foreach ($_POST['clientUrl'] AS $i => $clientUrl) {

						$tab = Array('clientUrl' => $clientUrl);

						if (isset($_POST['packageId'][$i]) && is_numeric($_POST['packageId'][$i])) {
							$tab['packageId'] = $_POST['packageId'][$i];
						}

						// lookup client url
						$clientArr = $this->setLinkUrl($clientUrl);
						if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
							$tab['clientId'] = $clientArr['Client']['clientId'];
							$tab['clientUrl'] = $clientUrl;
							$tabData[] = $tab;
						}

					}
					$merchDataJSON = json_encode($tabData);
					$dataArr = Array(
						'merchDataTypeId' => $homepageTabsDataType['MerchDataType']['id'],
						'merchDataGroupId' => $tabs[$tabName]['merchDataGroupId'],
						'startDate' => $_POST['data']['scheduleDate'],
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
							'MerchDataEntries.merchDataGroupId' => $tabs[$tabName]['merchDataGroupId'],
							'startDate' => $_POST['data']['scheduleDate']
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

		$others = $this->getOtherScheduled($inspirationDataType['MerchDataType']['id']);
		$this->set('others', $others);

		if (isset($_POST['data']['scheduleDate'])) {
			$this->set('scheduleDate', $_POST['data']['scheduleDate']);

			// get current data
			$params = Array(
				'recursive' => 0,
				'conditions' => Array(
					'merchDataTypeId' => $inspirationDataType['MerchDataType']['id'],
					'startDate' => $_POST['data']['scheduleDate']
				)
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
						if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
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
						'startDate' => $_POST['data']['scheduleDate'],
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
						'conditions' => Array(
							'merchDataTypeId' => $inspirationDataType['MerchDataType']['id'],
							'startDate' => $_POST['data']['scheduleDate']
						)
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

		$others = $this->getOtherScheduled($fauctionDataType['MerchDataType']['id']);
		$this->set('others', $others);

		if (isset($_POST['data']['scheduleDate'])) {
			$this->set('scheduleDate', $_POST['data']['scheduleDate']);

			// get current data
			$params = Array(
				'recursive' => 0,
				'conditions' => Array(
					'merchDataTypeId' => $fauctionDataType['MerchDataType']['id'],
					'startDate' => $_POST['data']['scheduleDate']
				)
			);
			$currData = $this->MerchDataEntries->find('first', $params);

			$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);

			if (isset($_POST['fauction']) && $_POST['fauction']) {

				if (isset($_POST['clientUrl']) && is_array($_POST['clientUrl'])) {
					$urlArr = Array();

					foreach ($_POST['clientUrl'] AS $url) {
						$cUrl = $url;
						$clientArr = $this->setLinkUrl($cUrl);
						if (is_array($clientArr) && isset($clientArr['Client']) && isset($clientArr['Client']['clientId'])) {
							$urlArr[] = Array(
								'clientId' => $clientArr['Client']['clientId'],
								'linkUrl' => $cUrl
							);
						}
					}

					$arr = Array('clients' => $urlArr);

					$dataArr = Array(
						'merchDataTypeId' => $fauctionDataType['MerchDataType']['id'],
						'startDate' => $_POST['data']['scheduleDate'],
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
						'conditions' => Array(
							'merchDataTypeId' => $fauctionDataType['MerchDataType']['id'],
							'startDate' => $_POST['data']['scheduleDate']
						)
					);
					$currData = $this->MerchDataEntries->find('first', $params);

					$this->set('currData', $currData['MerchDataEntries']['merchDataArr']);
					$this->set('dataSaved', true);
				}

			}
		}

	}

	public function fauctionld()
	{
		$merchDataTypeName = 'Listing & Destination Featured Auctions';
		$params = array(
			'recursive' => 0,
			'conditions' => array('merchDataTypeName' => $merchDataTypeName)
		);
		$fAuctionDataType = $this->MerchDataType->find('first', $params);
		if (isset($fAuctionDataType['MerchDataType']['id'])) {
			$merchDataTypeId = $fAuctionDataType['MerchDataType']['id'];
		} else {
			$this->MerchDataType->create();
			$this->MerchDataType->save(array(
				'siteId' => '1',
				'merchDataTypeName' => $merchDataTypeName,
				'dataColumnsCsv' => 'clientUrl'
			));
			$merchDataTypeId = $this->MerchDataType->getLastInsertId();
		}
		// Find future Schedule Dates
		$futureDates = array();
		foreach ($this->MerchDataEntries->find('all',array('recursive'=>0,'conditions'=>array('merchDataTypeId'=>$merchDataTypeId,'startDate >= DATE(NOW())'))) as $fds) {
			if (isset($fds['MerchDataEntries']['startDate'])) {
				$futureDates[] = $fds['MerchDataEntries']['startDate'];
			}
		}
		$futureDates = array_unique($futureDates);
		sort($futureDates);
		$this->set('futureDates', $futureDates);
		// Find last scheduled date
		$lastDate = $this->MerchDataEntries->find('first', array(
			'recursive' => 0,
			'order' => 'startDate DESC',
			'limit' => 1,
			'conditions' => array(
				'merchDataTypeId' => $merchDataTypeId,
				'startDate <= DATE(NOW())',
			),
		));
		if (isset($lastDate['MerchDataEntries']['startDate'])) {
			$this->set('lastDate', $lastDate['MerchDataEntries']['startDate']);
		}
		// See if a valid scheduled date is set
		$scheduleDate = null;
		if (isset($this->params['url']['data']['scheduleDate'])) {
			// Check for valid schedule date
			if (preg_match('/^20[0-9]{2}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[0-1])$/', trim($this->params['url']['data']['scheduleDate']))) {
				// We have a valid date
				$scheduleDate = trim($this->params['url']['data']['scheduleDate']);
				$this->set('scheduleDate', $scheduleDate);
				// See if this date is already scheduled
				$params = array(
					'recursive' => 0,
					'conditions' => array(
						'merchDataTypeId' => $merchDataTypeId,
						'startDate' => $scheduleDate,
					)
				);
				$merchEntry = $this->MerchDataEntries->find('first', $params);
				if (isset($merchEntry['MerchDataEntries']['id']) && $merchEntry['MerchDataEntries']['id']) {
					$merchDataEntryId = $merchEntry['MerchDataEntries']['id'];
					$merchDataJSON = $merchEntry['MerchDataEntries']['merchDataJSON'];
					$this->set('merchDataJSON', $merchDataJSON);
				} else {
					$merchDataEntryId = null;
				}

				// Save data if there is any
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
					$this->autoRender = false;
					$newId = null;
					$formData = array();
					if (isset($this->params['form']['saveData'])) {
						$saveData = array(
							'id' => $merchDataEntryId,
							'merchDataTypeId' => $merchDataTypeId,
							'merchDataGroupId' => null,
							'startDate' => $scheduleDate,
							'merchDataJSON' => json_encode($this->params['form']['saveData']),
							'isHeader' => 0,
						);
						$this->MerchDataEntries->create();
						$this->MerchDataEntries->id = $merchDataEntryId;
						$this->MerchDataEntries->save($saveData);
					} else {
						if ($merchDataEntryId) {
							$this->MerchDataEntries->delete($merchDataEntryId);
						}
					}
				}
			}
		}
	}

	public function featured_blog()
	{
		/* $defaultDate is hardcoded, but in the future we could set up scheduling of the
		 * blogs */
		$defaultDate = '2012-03-24';
		$merchDataTypeName = 'Featured Blog';
		$params = array(
			'recursive' => 0,
			'conditions' => array('merchDataTypeName' => $merchDataTypeName)
		);
		$fAuctionDataType = $this->MerchDataType->find('first', $params);
		if (isset($fAuctionDataType['MerchDataType']['id'])) {
			$merchDataTypeId = $fAuctionDataType['MerchDataType']['id'];
		} else {
			$this->MerchDataType->create();
			$this->MerchDataType->save(array(
				'siteId' => '1',
				'merchDataTypeName' => $merchDataTypeName,
				'dataColumnsCsv' => 'clientUrl'
			));
			$merchDataTypeId = $this->MerchDataType->getLastInsertId();
		}

		$params = array(
			'recursive' => 0,
			'conditions' => array(
				'merchDataTypeId' => $merchDataTypeId,
				'startDate' => $defaultDate
			)
		);
		$fBlogEntry = $this->MerchDataEntries->find('first', $params);
		if (isset($fBlogEntry['MerchDataEntries']['id'])) {
			$fBlogEntryId = $fBlogEntry['MerchDataEntries']['id'];
			$fBlogId = $fBlogEntry['MerchDataEntries']['merchDataArr']['featuredBlogId'];
		} else {
			$this->MerchDataEntries->create();
			$fBlogId = 1427;
			$this->MerchDataEntries->save(array(
				'merchDataTypeId' => $merchDataTypeId,
				'startDate' => $defaultDate,
				'merchDataJSON' => '{"featuredBlogId":' . $fBlogId . '}',
			));
			$fBlogEntryId = $this->MerchDataEntries->getLastInsertId();
		}

		// Save data if there is any
		if (isset($this->data['fBlogId']) && is_numeric($this->data['fBlogId'])) {
			$fBlogId = intval($this->data['fBlogId']);
			$saveData = array(
				'id' => $fBlogEntryId,
				'merchDataTypeId' => $merchDataTypeId,
				'merchDataGroupId' => null,
				'startDate' => $defaultDate,
				'merchDataJSON' => json_encode(array('featuredBlogId' => $fBlogId)),
				'isHeader' => 0,
			);
			$this->MerchDataEntries->create();
			$this->MerchDataEntries->id = $fBlogEntryId;
			$this->MerchDataEntries->save($saveData);

		}

		$this->set('fBlogId', $fBlogId);

	}

	private function getOtherScheduled($merchDataTypeId, $merchDataGroupId = null)
	{
		$result = Array();

		// current
		$current = $this->MerchDataEntries->find('first', Array(
			'recursive' => -1,
			'conditions' => Array(
				'isHeader' => 0,
				'merchDataTypeId' => $merchDataTypeId,
				'merchDataGroupId' => $merchDataGroupId,
				'startDate <= NOW()'
			),
			'order' => Array('startDate DESC')
		));
		if (is_array($current) && isset($current['MerchDataEntries']['merchDataArr'])) {
			$result['current'] = $current['MerchDataEntries'];
		} else {
			$result['current'] = false;
		}

		// next
		$next = $this->MerchDataEntries->find('first', Array(
			'recursive' => -1,
			'conditions' => Array(
				'isHeader' => 0,
				'merchDataTypeId' => $merchDataTypeId,
				'merchDataGroupId' => $merchDataGroupId,
				'startDate > NOW()'
			),
			'order' => Array('startDate ASC')
		));
		if (is_array($next) && isset($next['MerchDataEntries'])) {
			$result['next'] = $next['MerchDataEntries'];
		} else {
			$result['next'] = false;
		}

		return $result;
	}

	private function getClient($clientId)
	{
		$params = Array(
			'recursive' => 0,
			'conditions' => Array('clientId' => $clientId)
		);
		return $this->Client->find('all', $params);
	}

	// alters linkUrl and tries returns client info if found from seo-name
	private function setLinkUrl(&$linkUrl)
	{

		// TICKET3267 was here

		// make link urls relative
		if (!empty($linkUrl)) {
			if (($urlStr = str_replace('http://www.luxurylink.com/', '/', $linkUrl)) || ($urlStr = str_replace('www.luxurylink.com/', '/', $linkUrl)) || (($urlStr = substr($linkUrl, 0, 1)) == '/')) {
				// try lookup of client info
				if (substr($urlStr, 0, 10) == '/fivestar/') {
					$linkUrl = $urlStr;
					return $this->Client->getClientBySeoUrl(str_replace('/fivestar/', '', $urlStr));
				}
			}
		}
	}

	private function _filterImageUrl($url) {
		$possible = array('www.luxurylink.com','luxurylink.com','img.llsrv.us','ui.llsrv.us','photos.luxurylink.us');
		foreach ($possible as $p) {
			$url=preg_replace('/^http:\/\/'.str_replace('.','\.',$p).'\//','/',$url);
		}
		$url = preg_replace('/^\//','http://photos.luxurylink.us/',$url);
		return $url;
	}

}
