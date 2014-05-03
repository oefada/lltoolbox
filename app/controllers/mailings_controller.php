<?php

//App::import('Vendor', '');

class MailingsController extends AppController {

	var $name = 'Mailings';
	var $scaffold;
	var $helpers=array('mailing');
    
    function beforeFilter() {
		parent::beforeFilter();
        $users = array();
        $superusers = array('jlagraff', 'pkaelin');
        $adusers = array('sgreen', 'gmaltzman', 'sflax');
        $supergroups = array('Geeks');
        $this->set('superusers', $superusers);
        $this->set('adusers', $adusers);
				$this->set('currentTab', 'newsletters');
        $this->set('hideSidebar', true);
	}

  function index() {

	}

	/**
	 * Generate a list of test clients and/or present form for user entry of clients
	 * 
	 * 
	 * 
	 * @return null 
	 */
	function generator(){

		$templateId = (isset($this->params['url']['tid'])) ? $this->params['url']['tid'] : false;

		$client_arr=array();

		// populate the fields with clients
		if (isset($this->params['url']['test']) && $this->params['url']['test']==1){

			$offerTable=($templateId=='fg1')?'offerFamily':'offerLuxuryLink';
			$like=($templateId=='fg1')?'%family':'luxurylink%';
	
			$q="SELECT c.* FROM client `c` INNER JOIN $offerTable using (clientId) ";
			$q.="WHERE endDate>NOW() AND sites like '$like' ";
			$q.="AND offerTypeId!=7 ";
			$q.="GROUP BY clientId ";
			$q.="ORDER BY offerId DESC ";
			$q.="LIMIT 15";
			$rows=$this->Mailing->query($q);
			foreach($rows as $key=>$row){
				foreach($row as $arr){
					$client_arr[$arr['clientId']]=$arr['name'];
				}
			}
			$this->set('client_arr',$client_arr);	
		}

		$this->set('templateId', $templateId);

	}

	function generated(){



		if (!empty($this->params['form']['clientId_arr'])){

			$templateId = (isset($this->params['form']['tid'])) ? $this->params['form']['tid'] : false;
			$siteId = ($templateId == 'fg1') ? 2 : 1;

			$month=$this->params['form']['month'];
			$day=$this->params['form']['day'];
			$year=$this->params['form']['year'];
			$date_ymd=$year.$month.$day;
			$this->set("date_ymd",$date_ymd);

			$version=$this->params['form']['version'];
			$this->set("version",$version);

			$clientId_arr=$this->params['form']['clientId_arr'];
			$clientName_arr=$this->params['form']['clientName'];
			$error=false;
			foreach($clientId_arr as $key=>$id){
				if ($templateId == 'fg1') {
					if (trim($id)=='') {
						unset($clientId_arr[$key]);	
					}
				} else {
					if (trim($id)=='')$error=true;	
				}
			}
			if ($error){
				echo "<p>The following clientId's were submitted:</p>";
				foreach($clientId_arr as $key=>$id){
					echo ($key+1).".) clientId: $id clientName: ".$clientName_arr[$key]."<br>";
				}
				exit("<p style='color:red;'>You must submit 15 clients.</p>");
			}
			$this->layout=false;
			$rows=$this->Mailing->generator($clientId_arr, $siteId);	
			//AppModel::printR($rows);exit;
			$this->set('rows',$rows);

			$this->set('url', 'http://www.luxurylink.com');
            $utmArr['showLeader'] = '1';
			$utmArr['utm_medium']='news';
			$utmArr['utm_source']='insider';
			$utmArr['utm_campaign']='insider_'.$date_ymd.$version;
			// utm_content is set per item in the view
			// utm_term is set per item in the view
			$this->set('utmArr', $utmArr);

			if ($templateId == 'fg1') {
				$this->render('generated_fg1');
			}elseif ($templateId=='inspiration'){
                //There is a lot of code. The views are all doen differently. Let's continue doing it this way for now.
                $utmArr['showLeader'] = '1';
                $utmArr['utm_source']='inspiration';
                $utmArr['utm_campaign']='inspiration_'.$date_ymd.$version;
                // utm_content is set per item in the view
                // utm_term is set per item in the view
                $this->set('utmArr', $utmArr);
				$this->render('generated_inspiration_new');
			}
			
		}
	}

	function mailings(){
    
        $this->paginate();
        $conditions = array('conditions' => array());
        if (!empty($this->data)) {
            $conditions['conditions'] = array('Mailing.mailingDate' => $this->data['Mailing']['mailingDate']);
        }
        $mailings = $this->Mailing->find('all', array($conditions, 'order' => 'mailingDate DESC', 'limit' => 52));
        foreach ($mailings as &$mailing) {
            $this->Mailing->MailingPackageSectionRel->MailingSection->recursive = -1;
            $sections = $this->Mailing->MailingPackageSectionRel->MailingSection->find('all', array('conditions' => array('mailingTypeId' => $mailing['Mailing']['mailingTypeId'])));
            if (!empty($sections)) {
                foreach ($sections as $section) {
                    $sectionCount = $this->Mailing->MailingPackageSectionRel->find('count', array('conditions' => array('MailingPackageSectionRel.mailingId' => $mailing['Mailing']['mailingId'], 'MailingPackageSectionRel.mailingSectionId' => $section['MailingSection']['mailingSectionId'])));
                    $mailing['MailingSection'][$section['MailingSection']['mailingSectionName']]['availableSlots'] = ($section['MailingSection']['maxInsertions'] * $section['MailingSection']['maxVariations']) - $sectionCount;
                }
            }
            
        }
        $this->set('mailings', $mailings);
    }
    
    function edit($mailingId) {
        $this->layout = 'default_jquery';
        if (!empty($this->data)) {
            if ($this->Mailing->saveAll($this->data)) {
                $this->Session->setFlash('This mailing has been saved');
            }
            else {
                $this->Session->setFlash('This mailing could not be saved.');
            }
        }
        $this->Mailing->recursive = 2;
        $mailing = $this->Mailing->findByMailingId($mailingId);
        
        foreach($mailing['MailingType']['MailingSection'] as &$section) {
            if (!empty($mailing['MailingPackageSectionRel'])) {
                $sectionVariations = $this->getVariationsForSection($mailingId, $section['mailingSectionId']);
                $section['Variations'] = $sectionVariations;
            }
            if (empty($section['Variations']) && !empty($section['mailingSectionContent'])) {
                switch($section['mailingSectionContent']) {
                    case 'Most Popular':
                    default:
                        switch($mailing['MailingType']['siteId']) {
                            case 2:     //family
                                $offerTable = 'offerFamily';
                                break;
                            case 1:     //LL
                            default:
                                $offerTable = 'offerLuxuryLink';
                        }
                        $query = "SELECT MIN(IF(sm.offerTypeId IN(1,2,6),sm.openingBid,sm.buyNowPrice)) price,
                                  Client.clientId,
                                  Client.name,
                                  l.endDate,
                                  Package.packageId,
                                  Package.packageName,
                                  Package.packageTitle
                                  FROM package Package
                                  INNER JOIN clientLoaPackageRel cp ON Package.packageId = cp.packageId
                                  INNER JOIN client Client ON cp.clientId = Client.clientId
                                  INNER JOIN loa l ON cp.loaId = l.loaId AND NOW() BETWEEN l.startDate AND l.endDate AND l.loaLevelId = 2
                                  INNER JOIN schedulingMaster sm ON Package.packageId = sm.packageId
                                  INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId
                                  INNER JOIN (
                                    SELECT packageId
                                    FROM ticket
                                    WHERE created BETWEEN NOW() - INTERVAL 30 DAY AND NOW() GROUP BY packageId HAVING COUNT(*) > 3
                                    ) AS popularPackages ON Package.packageId = popularPackages.packageId
                                  WHERE '{$mailing['Mailing']['mailingDate']}' BETWEEN DATE(si.startDate) AND si.endDate
                                  AND si.endDate > '{$mailing['Mailing']['mailingDate']}' + INTERVAL 2 DAY
                                  AND l.numEmailInclusions > l.numEmailsSent
                                  AND Client.clientId NOT IN (
                                    SELECT clientId
                                    FROM mailingPackageSectionRel
                                    INNER JOIN mailing USING (mailingId)
                                    WHERE mailingDate BETWEEN '{$mailing['Mailing']['mailingDate']}' - INTERVAL 30 DAY AND '{$mailing['Mailing']['mailingDate']}'
                                  )
                                  GROUP BY Package.packageId
                                  ORDER BY price
                                  LIMIT {$section['maxInsertions']}";
                        $content = $this->Mailing->query($query);
                        break;
                }
                
                $section['Variations']['A'] = $content;
            }
        }
        $mailing['Mailing']['300x250'] = $this->Mailing->MailingAdvertising->getBigAds($mailingId);
        $this->set('mailing', $mailing);
    }
    
    function add() {
        if (!empty($this->data)) {
            $this->data['Mailing']['siteId'] = $this->Mailing->MailingType->field('siteId', array('mailingTypeId' => $this->data['Mailing']['mailingTypeId']));
            if ($mailing = $this->Mailing->save($this->data)) {
                $this->redirect('/mailings/edit/'.$this->Mailing->getLastInsertId());
            }
            else {
                $this->Session->setFlash('This mailing could not be created');
            }
        }
        $mailingTypes = $this->Mailing->MailingType->find('list', array('fields' => 'MailingType.mailingTypeName',
                                                                        'order' => 'MailingType.mailingTypeName'));
        $this->set('mailingTypes', $mailingTypes);
    }
    
    function suggestClients($mailingId) {
        $this->autoRender = false;
        $fields = array('clientId', 'name');
        $results = $this->Mailing->MailingPackageSectionRel->getAvailableClients($_GET['q'], $mailingId, $_GET['sectionId'], $_GET['variationId']);
        $list = $this->prepList($results, 'Client');
        echo print_r($list, true);
    }
    
    function addClients() {
        $this->autoRender = false;
        $clients = $this->data;
        if (!empty($this->data)) {
            if (count($clients) == 1) {
                 if ($m = $this->Mailing->MailingPackageSectionRel->saveClient($this->data[0])) {
                    $this->Mailing->MailingPackageSectionRel->contain = array('Client');
                    $client = $this->Mailing->MailingPackageSectionRel->find('first', array('conditions' => array('MailingPackageSectionRel.clientId' => $m['MailingPackageSectionRel']['clientId'],
                                                                                                                  'MailingPackageSectionRel.mailingSectionId' => $m['MailingPackageSectionRel']['mailingSectionId'],
                                                                                                                  'MailingPackageSectionRel.variation' => $m['MailingPackageSectionRel']['variation']),
                                                                                            )
                                                                             );
                    $this->set('mailingPackageSectionRelId', $client['MailingPackageSectionRel']['mailingPackageSectionRelId']);
                    $this->set('name', $client['Client']['name']);
                    $this->render('/elements/mailing_scheduler/list_item');
                }
                else {
                    $this->Session->setFlash('Client could not be added');
                    $this->redirect('/mailings/edit/'.$mailingId);
                }
            }
            else {
                $mailingId = $clients[0]['mailingId'];
                $sectionId = $clients[0]['mailingSectionId'];
                $variationId = $clients[0]['variation'];
                foreach($clients as $client) {
                    $this->Mailing->MailingPackageSectionRel->saveClient($client);
                }
                $this->Mailing->MailingPackageSectionRel->recursive = -2;
                $mailingClients = $this->Mailing->MailingPackageSectionRel->find('all', array('conditions' => array('MailingPackageSectionRel.mailingSectionId' => $sectionId,
                                                                                                                    'MailingPackageSectionRel.mailingId' => $mailingId)));
                $this->set('clients', $mailingClients);
                $this->set('variationId', $variationId);
                $this->set('mailingId', $mailingId);
                $this->set('sectionId', $sectionId);
                $this->render('/elements/mailing_scheduler/client_picker');
            }
        }
    }
    
    function prepList($data, $model) {
        $list = array();
        if (!empty($data)) {
            foreach($data as $d) {
                $item = $d[$model]['name'].'|'.$d[$model][low($model).'Id'];
                array_push($list, $item);
            }
        }
        return implode("\n", $list);
    }
    
    function getVariationsForSection($mailingId, $sectionId) {
        $query = "SELECT DISTINCT(variation) FROM mailingPackageSectionRel WHERE mailingId = {$mailingId} AND mailingSectionId = {$sectionId} ORDER BY variation";
        $variations = $this->Mailing->query($query);
        $variationsList = array();
        if (!empty($variations)) {
            foreach($variations as $variation) {
                $variationClients = $this->Mailing->MailingType->MailingSection->getVariations($mailingId, $sectionId, $variation['mailingPackageSectionRel']['variation']);
                $variationsList[$variation['mailingPackageSectionRel']['variation']] = $variationClients;
            }
            return $variationsList;
        }
    }
    
    function addVariationToSection($mailingId, $sectionId) {
        $this->autoRender = false;
        $variations = $this->getVariationsForSection($mailingId, $sectionId);
        if(empty($variations)) {
            $newVariationId = 'B';
        }
        else {
            $rVariations = array_reverse(array_keys($variations));
            $newVariationId = $rVariations[0];
            $newVariationId++;
        }
        $this->set('mailingId', $mailingId);
        $this->set('sectionId', $sectionId);
        $this->set('variationId', $newVariationId);
        $this->set('maxInsertions', $this->Mailing->MailingPackageSectionRel->MailingSection->field('maxInsertions', array('mailingSectionId' => $sectionId)));
        $this->set('clients', array());
        $this->render('/elements/mailing_scheduler/client_picker');
    }
    
    function setSortOrder() {
        $this->autoRender = false;
        if (!empty($this->params['url']['listItem'])) {
            $i = 1;
            foreach($this->params['url']['listItem'] as $item) {
                $query = "UPDATE mailingPackageSectionRel SET sortOrder = {$i} WHERE mailingPackageSectionRelId = {$item}";
                $this->Mailing->query($query);
                $i++;
                $client = $this->Mailing->MailingPackageSectionRel->find('first', array('conditions' => array('MailingPackageSectionRel.mailingPackageSectionRelId' => $item)));
                $this->set('mailingPackageSectionRelId', $item);
                $this->set('name', $client['Client']['name']);
                $this->render('/elements/mailing_scheduler/list_item');
            }
        }
    }
    
    function deleteFromVariation($mailingPackageSectionRelId) {
        $this->autoRender = false;
        $this->Mailing->MailingPackageSectionRel->delete($mailingPackageSectionRelId);
        echo $mailingPackageSectionRelId;
    }
    
    function saveAd() {
        $this->autoRender = false;
        if (!empty($this->data)) {
            $imagePath = $this->data['MailingAdvertising']['imageUrl'];
            if ($this->Mailing->MailingAdvertising->save($this->data)) {
                $out = '<img src="'.$imagePath.'" />';
                $outArr = array('imagePath' => $out,
                                'newAdId' => $this->Mailing->MailingAdvertising->getLastInsertId());
                echo json_encode($outArr);
            }
        }
    }
    
    function saveMarketplace() {
        $this->autoRender = false;
        if (!empty($this->data)) {
            $mailingId = $this->data['MailingAdvertising'][0]['mailingId'];
            $i = 0;
            foreach($this->data['MailingAdvertising'] as &$item) {
                if ($this->Mailing->MailingAdvertising->fieldsComplete($item)) {
                    if (!stristr($item['imageUrl'], 'http://www.luxurylink.com') && !empty($item['imageUrl'])) {
                        $path = "http://www.luxurylink.com";
                        if (strpos($item['imageUrl'], '/') != 0 || strpos($item['imageUrl'], '/') === false) {
                            $path .= '/';
                        }
                        $item['imageUrl'] = $path.$item['imageUrl'];
                    }
                }
                else {
                    unset($this->data['MailingAdvertising'][$i]);
                }
                $i++;
            }
            if ($this->Mailing->MailingAdvertising->saveMarketplace($this->data)) {
                $this->redirect('/mailings/edit/'.$mailingId);
            }
        }
    }
    
}
?>
