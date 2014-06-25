<?php
class LoasController extends AppController
{
    public $name = 'Loas';
    public $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number', 'Paginator');
    public $uses = array('Loa', 'LoaItemRatePeriod', 'SchedulingMasterTrackRel', 'LoaText','LoaDocument');
    public $paginate;

    /**
     *
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->set('currentTab', 'property');
        $this->set('searchController', 'client');
    }

    public function maintTracking($id = null)
    {

        $this->Loa->recursive = 0;
        $loa = $this->data = $this->Loa->read(null, $id);
        $tracks = array();
        $track_details = array();

        $offerLiveFieldArr = array(
            'offerId',
            'packageId',
            'offerTypeName',
            'offerSubtitle',
            'startDate',
            'endDate',
            'retailValue',
            'openingBid'
        );

        foreach ($offerLiveFieldArr as $field) {
            $options['fields'][] = 'offerLive.' . $field;
        }

        $dir = '';
        $sort = '';
        if (isset($this->passedArgs['sort'])) {
            $sort = $this->passedArgs['sort'];
            $dir = $this->passedArgs['direction'];
        } else {
            $sort = 'ticketId';
            $dir = 'desc';
        }

        $q = 'SELECT * FROM track WHERE loaId =?';
        $tracks_result = $this->Loa->query($q, array($loa['Loa']['loaId']));

        foreach ($tracks_result as $track) {

            $trackId = $track['track']['trackId'];
            $tracks[$trackId] = $track['track'];

            $offers = array();
            foreach ($loa['Loa']['sites'] as $site) {

                $join_table = ($site == 'luxurylink') ? 'offerLuxuryLink' : 'offerFamily';

                $options['conditions'] = array('SchedulingMasterTrackRel.trackId' => $track['track']['trackId']);

                $options['joins'] = array(
                    array(
                        'table' => 'schedulingMaster',
                        'alias' => 'sm',
                        'type' => 'inner',
                        'conditions' => 'sm.schedulingMasterId = SchedulingMasterTrackRel.schedulingMasterId'
                    ),
                    array(
                        'table' => 'schedulingInstance',
                        'type' => 'inner',
                        'alias' => 'si',
                        'conditions' => 'si.schedulingMasterId = SchedulingMasterTrackRel.schedulingMasterId'
                    ),
                    array(
                        'table' => 'offer',
                        'alias' => 'o',
                        'type' => 'inner',
                        'conditions' => 'o.schedulingInstanceId = si.schedulingInstanceId'
                    ),
                    array(
                        'table' => $join_table,
                        'alias' => 'offerLive',
                        'type' => 'inner',
                        'conditions' => 'offerLive.offerId=o.offerId'
                    )
                );

                $offer_result = $this->SchedulingMasterTrackRel->find('all', $options);

                foreach ($offer_result as $offer) {
                    $offers[] = $offer['offerLive'];
                }

            }

            $tracks[$trackId]['offers'] = $offers;

        }

        if (!empty($tracks)) {

            //print "<pre>";print_r($tracks);exit;
            $q = 'SELECT trackDetail.*, ticket.offerId, ticket.numNights as numNights ';
            $q .= "FROM trackDetail INNER JOIN ticket USING (ticketId) ";
            $q .= 'WHERE trackId IN (' . implode(',', array_keys($tracks)) . ') ';
            $q .= 'ORDER BY trackId ASC';
            $track_details_result = $this->Loa->query($q);
            foreach ($track_details_result as $track_detail) {
                $oid = $track_detail['ticket']['offerId'];
                $track_details[$oid][] = $track_detail['trackDetail'];
            }

            //print '<pre>';print_r($track_details);exit;
            // $tracks['offers'] has a one to many relationship with $track_details.
            // That is, there will be one offerId in $tracks to potentially many ticketId's in $track_details.
            // Make it one to one so as to be able to sort by ticketId.
            $new_tracks = array();
            foreach ($track_details as $oid => $track_detail) {
                foreach ($track_detail as $i => $detail) {
                    foreach ($tracks as $trackId => $arr) {
                        foreach ($arr['offers'] as $j => $row) {
                            $found = false;
                            if ($oid == $row['offerId']) {
                                $found = true;
                                // don't overwrite existing offerId
                                if (!isset($row['ticketId'])) {
                                    $tmp = array_merge($detail, $row);
                                    $tracks[$trackId]['offers'][$j] = $tmp;
                                } else {
                                    $tmp = $detail + $row;
                                    $tracks[$trackId]['offers'][] = $tmp;
                                }
                                unset($track_details[$oid][$i]);
                                break;
                            }
                        }
                    }
                }
            }

            // Set any offer without a ticketId to hold empty track details
            $track_detail_empty = array(
                'trackDetailId' => 0,
                'trackId' => 0,
                'ticketId' => 0,
                'ticketAmount' => 0,
                'allocatedAmount' => 0,
                'iteration' => 0,
                'cycle' => 0,
                'amountKept' => 0,
                'amountRemitted' => 0,
                'xyRunningTotal' => 0,
                'xyAverage' => 0,
                'keepBalDue' => 0,
                'oaBalance' => 0,
                'created' => 0,
                'modified' => 0,
                'initials' => 0
            );

            foreach ($tracks as $trackId => $arr) {
                foreach ($arr['offers'] as $j => $row) {
                    if (!isset($row['ticketId'])) {
                        $tracks[$trackId]['offers'][$j] += $track_detail_empty;
                    }
                }
            }

            // sort by ticketId's
            if ($sort == 'ticketId') {

                // order the ticketId's within each track
                function ticketIdDesc_cmp($a, $b)
                {
                    if ($a['ticketId'] == $b['ticketId']) {
                        return 0;
                    }
                    return ($a['ticketId'] < $b['ticketId']) ? 1 : -1;
                }

                function ticketIdAsc_cmp($a, $b)
                {
                    if ($a['ticketId'] == $b['ticketId']) {
                        return 0;
                    }
                    return ($a['ticketId'] > $b['ticketId']) ? 1 : -1;
                }

                foreach ($tracks as $trackId => $arr) {
                    foreach ($arr['offers'] as $j => $offers) {
                        if ($dir == 'desc') {
                            usort($tracks[$trackId]['offers'], 'ticketIdDesc_cmp');
                        } else {
                            usort($tracks[$trackId]['offers'], 'ticketIdAsc_cmp');
                        }
                    }
                }

            }

            $trackWarning = false;
        } else {
            $trackWarning = '<h3 style="font-size:15px;">*** NO TRACK IS SETUP FOR THIS LOA ***</h3><br /><br />';
        }

        $this->set('trackWarning', $trackWarning);
        $this->set('loa', $loa);
        $this->set('tracks', $tracks);
        $this->set('track_details', $track_details);
        $this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));

    }

    public function index($clientId = null)
    {
        if (isset($clientId) && $this->Loa->Client->find(
                'count',
                array('conditions' => array('Client.clientId' => $clientId))
            )
        ) {
            $this->Loa->recursive = 0;
            $this->set('loas', $this->paginate('Loa', array('Client.clientId' => $clientId)));
        } else {
            $this->cakeError('error404');
        }

        $this->set('client', $this->Loa->Client->findByClientId($clientId));
        $this->set('clientId', $clientId);
    }

    public function view($id = null)
    {
        $this->redirect(array('action' => 'edit', $id));
    }

    public function add($clientId = null)
    {

        if (!empty($this->data)) {
            $clientId = $this->data['Loa']['clientId'];
            $this->data['Loa']['membershipBalance'] = $this->data['Loa']['membershipFee'];
            $this->data['Loa']['membershipPackagesRemaining'] = $this->data['Loa']['membershipTotalPackages'];
            $this->data['Loa']['numberPackagesRemaining'] = $this->data['Loa']['loaNumberPackages'];
            if ($this->data['Loa']['loaMembershipTypeId'] == 5) {
                // TEMP for booking credit
                $this->data['Loa']['retailValueBalance'] = $this->data['Loa']['retailValueFee'];
            }
            // ticket 3404 - check for date conflicts
            $conflictLoa = $this->findDateConflict($clientId, implode('/', $this->data['Loa']['startDate']));
            if ($conflictLoa !== false) {
                $msg = 'The new dates overlap with LOA <a href="/loas/edit/' . $conflictLoa . '">' . $conflictLoa . '</a>.';
                $this->Session->setFlash(__($msg, true));
            } else {
                $this->Loa->create();
                if (isset($this->data['Loa']['checkboxes']) && is_array($this->data['Loa']['checkboxes'])) {
                    $this->data['Loa']['checkboxes'] = implode(',', $this->data['Loa']['checkboxes']);
                }
                if (($r = $this->Loa->save($this->data)) !== false) {
                    $this->Session->setFlash(__('The Loa has been saved', true));
                    $this->redirect("/clients/$clientId/loas");
                } else {
                    $msg = 'The Loa could not be saved. Please, try again.<br>';
                    $msg .= implode("<br>", array_values($this->Loa->invalidFields()));
                    $this->Session->setFlash(__($msg, true));
                }
            }
        }

        if (!$clientId) {
            $this->Session->setFlash(__('Incorrect client id specified. Please try again.', true));
            $this->redirect(array('controller' => 'clients', 'action' => 'index'));
        }
        $this->data['Loa']['clientId'] = $clientId;
        $this->Loa->Client->recursive = 1;
        $client = $this->Loa->Client->find('Client.clientId = ' . $clientId);

        foreach ($client['Loa'] as $key => $arr) {
            $client['Loa'][$key]['checkboxes'] = explode(",", $arr['checkboxes']);
        }

        $currencyIds = $this->Loa->Currency->find('list');
        $loaLevelIds = $this->Loa->LoaLevel->find('list', array('order' => array('LoaLevel.dropdownSortOrder')));
        $loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
        $loaPaymentTermIds = $this->Loa->LoaPaymentTerm->find('list');
        $loaInstallmentTypeIds = $this->Loa->LoaInstallmentType->find('list');
        $accountTypeIds = $this->Loa->AccountType->find('list');

        $this->Loa->LoaPublishingStatusRel->PublishingStatus->recursive = -1;
        $publishingStatus = $this->Loa->LoaPublishingStatusRel->PublishingStatus->find('list');

        $salesPeople =  $this->Loa->getSalesPeople();

        if (!empty($salesPeople)){
            $salesPeopleAutoComplete =array();
            $i =0;
            foreach ($salesPeople as $username=>$fullName){
                $fullName = str_replace("\r", "", $fullName);
                $fullName = str_replace("\n", "", $fullName);
                $salesPeopleAutoComplete[]= array('value'=>$username,'label'=>addslashes($fullName));
            }
        }
        $this->set('listSalesPeople',$salesPeopleAutoComplete);
        $this->set('checkboxValuesArr', $this->getCheckboxValuesArr());
        $this->set('clientName', $client['Client']['name']);
        $this->set('client', $this->Loa->Client->findByClientId($clientId));
        $this->set('loaPaymentTermIds', $loaPaymentTermIds);
        $this->set('loaInstallmentTypeIds',$loaInstallmentTypeIds);
        $this->set('currencyIds', $currencyIds);
        $this->set('loaLevelIds', $loaLevelIds);
        $this->set('loaMembershipTypeIds', $loaMembershipTypeIds);
        $this->set('publishingStatus', $publishingStatus);
        $this->set('accountTypeIds', $accountTypeIds);
    }

    public function findDateConflict($clientId, $startDate)
    {
        $startTime = strtotime($startDate);
        $loas = $this->Loa->getClientLoas($clientId);
        foreach ($loas as $loa) {
            if (($startTime >= strtotime($loa['Loa']['startDate'])) && ($startTime <= strtotime(
                        $loa['Loa']['endDate']
                    ))
            ) {
                return $loa['Loa']['loaId'];
            }
        }
        return false;
    }

    /**
     * Values for checkboxes in 'checkbox' column
     *
     * @return array
     */
    public function getCheckboxValuesArr()
    {
        return array(
            'New York Times' => 'New York Times',
            'Departures (American Express)' => 'Departures (American Express)',
            'Exclusive Email' => 'Exclusive Email',
            'Includes CC Fee' => 'Includes CC Fee'
        );
    }

    public function items($id = null)
    {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Loa', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->Loa->recursive = 2;
        $this->data = $this->Loa->read(null, $id);
        foreach ($this->data['LoaItem'] as $k => $item) {
            if (!empty($item['LoaItemRatePeriod'])) {
                foreach ($item['LoaItemRatePeriod'] as $a => $rp) {
                    $tmp = $this->LoaItemRatePeriod->read(null, $rp['loaItemRatePeriodId']);
                    $this->data['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemRate'] = $tmp['LoaItemRate'];
                    $this->data['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemDate'] = $tmp['LoaItemDate'];
                }
            }
            if (!empty($item['LoaItemGroup'])) {
                $this->data['LoaItem'][$k]['isGroup'] = 1;
                $this->data['LoaItem'][] = $this->data['LoaItem'][$k];
                unset($this->data['LoaItem'][$k]);
            }
        }
        $this->data['Client']['Loa'] = array_reverse($this->data['Client']['Loa']);
        $this->data['LoaItem'] = $this->sortItems($this->data['LoaItem']);

        $this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
        $this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
        $this->set('day_map', array(0 => 'Su', 1 => 'M', 2 => 'Tu', 3 => 'W', 4 => 'Th', 5 => 'F', 6 => 'Sa'));
    }

    public function sortItems($data)
    {
        $loaItemTypeIds = array(19, 1, 6, 7, 5, 8, 15, 16, 3, 17, 18, 11);
        $tmp = array();
        $ids = array();
        foreach ($loaItemTypeIds as $itemTypeId) {
            foreach ($data as $k => $v) {
                if ($v['loaItemTypeId'] == $itemTypeId && (!in_array($v['loaItemId'], $ids))) {
                    $tmp[] = $v;
                    $ids[] = $v['loaItemId'];
                    unset($data[$k]);
                }
            }
        }
        $tmp = array_merge($tmp, $data);
        return $tmp;
    }

    public function edit($id = null)
    {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Loa', true));
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->data)) {

            $client = $this->Loa->Client->findByClientId($this->data['Loa']['clientId']);
            $this->data['Client'] = $client['Client'];

            if (is_array($this->data['Loa']['checkboxes'])){
                $this->data['Loa']['checkboxes'] = implode(",", $this->data['Loa']['checkboxes']);
            }

            $this->data['Loa']['modifiedBy'] = $this->user['LdapUser']['username'];

            $this->data['Loa']['sites'] =  array('luxurylink');

            if($this->Loa->save($this->data)){
                if ($this->data['Loa']['loaLevelId_prev'] !== $this->data['Loa']['loaLevelId']){
                    //LOA level has changed
                    if(4 == $this->data['Loa']['loaLevelId']){
                        //4- Agreement
                        $loaSubject = 'New LOA Created in Toolbox - Loa Submission - ' . $this->data['Client']['name'] . ' (Client Id: ' . $this->data['Client']['clientId'] . ')'; 
                        $this->Loa->changeEmail($this->data, $loaSubject, 'renew@luxurylink.com');
                    }
                }
                $this->Session->setFlash(__('The Loa has been saved', true));
                $this->redirect(array('action' => 'edit', $this->data['Loa']['loaId']));
            } else{
                $loa = $this->Loa->find($this->data['Loa']['loaId']);
                $this->data['Client'] = $loa['Client'];
                $this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
            }
        }
        $this->Loa->recursive = 2;
        if (empty($this->data)) {
            $this->data = $this->Loa->read(null, $id);
            if ($this->data) {
                usort($this->data['LoaItem'], array($this, 'sortLoaItemsByType'));
            }
        }
        $currencyIds = $this->Loa->Currency->find('list');
        $loaLevelIds = $this->Loa->LoaLevel->find('list', array('order' => array('LoaLevel.dropdownSortOrder')));
        $loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
        $this->Loa->LoaPublishingStatusRel->PublishingStatus->recursive = -1;
        $publishingStatus = $this->Loa->LoaPublishingStatusRel->PublishingStatus->find('list');
        $accountTypeIds = $this->Loa->AccountType->find('list');
        $completedStatusLL = array();
        $completedStatusFG = array();
        if (!empty($this->data['LoaPublishingStatusRel'])) {
            foreach ($this->data['LoaPublishingStatusRel'] as $pStatus) {
                if ($pStatus['site'] == 'luxurylink') {
                    $completedStatusLL[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
                } else {
                    $completedStatusFG[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
                }
            }
        }

        // get Renewal Result Options
        $loaTextResult = $this->LoaText->getLoaText(1);
        foreach ($loaTextResult as $loaText) {
            $renewalResultOptions[$loaText['loaText']['loaTextId']] = $loaText['loaText']['loaText'];
        }

        // get non Renewal Reason Options
        $nonRenewalReasonOptionsResult = $this->LoaText->getLoaText(2);
        foreach ($nonRenewalReasonOptionsResult as $loaText) {
            $nonRenewalReasonOptions[$loaText['loaText']['loaTextId']] = $loaText['loaText']['loaText'];
        }

        $this->set('renewalResultOptions', $renewalResultOptions);
        $this->set('nonRenewalReasonOptions', $nonRenewalReasonOptions);

        $this->set(
            compact(
                'currencyIds',
                'loaLevelIds',
                'loaMembershipTypeIds',
                'publishingStatus',
                'completedStatusLL',
                'completedStatusFG',
                'accountTypeIds'
            )
        );
        $client = $this->Loa->Client->findByClientId($this->data['Loa']['clientId']);

        $checkboxValuesSelectedArr = array();
        foreach ($client['Loa'] as $key => $arr) {
            if ($arr['loaId'] == $id) {
                $checkboxValuesSelectedArr = explode(",", $arr['checkboxes']);
            }
        }
        $salesPeople =  $this->Loa->getSalesPeople();

        if (!empty($salesPeople)){
            $salesPeopleAutoComplete =array();
            $i =0;
            foreach ($salesPeople as $username=>$fullName){
                $fullName = str_replace("\r", "", $fullName);
                $fullName = str_replace("\n", "", $fullName);
                $salesPeopleAutoComplete[]= array('value'=>$username,'label'=>addslashes($fullName));
            }
        }
        $this->set('listSalesPeople',$salesPeopleAutoComplete);
        $this->set('client', $client);
        $this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
        $this->set('checkboxValuesArr', $this->getCheckboxValuesArr());
        $this->set('checkboxValuesSelectedArr', $checkboxValuesSelectedArr);
        $this->set('loaPaymentTermIds', $this->Loa->LoaPaymentTerm->find('list'));
        $this->set('loaInstallmentTypeIds', $this->Loa->LoaInstallmentType->find('list'));
        $this->set('loaAudit',$this->Loa->findLog(array('model'=>'Loa','model_id'=>$this->data['Loa']['loaId'])));
    }

    public function sortLoaItemsByType($a, $b)
    {
        if ($a['LoaItemType']['loaItemTypeName'] == $b['LoaItemType']['loaItemTypeName']) {
            return 0;
        }
        return ($a['LoaItemType']['loaItemTypeName'] < $b['LoaItemType']['loaItemTypeName']) ? -1 : 1;
    }


    public function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Loa', true));
            $this->redirect(array('controller' => 'clients', 'action' => 'index'));
        }
        $this->Loa->recursive = -1;
        $clientId = $this->Loa->read('Loa.clientId', $id);
        $clientId = $clientId['Loa']['clientId'];

        if ($this->Loa->del($id)) {
            $this->Session->setFlash(__('Loa deleted', true));
            $this->redirect("/clients/$clientId/loas");
        }
    }

    /**
     * Finds an LOA by id and gets the expiration date
     * Mainly used as an ajax call from the package interface
     * @params $loaId the id of the loa to find the expiration date of
     * @returns the expiration date
     */
    public function getExpiration($loaId = null)
    {
        $this->autoRender = false;

        if (!empty($this->data['ClientLoaPackageRel']) && null === $loaId) {
            $clientLoaPackageRel = array_pop($this->data['ClientLoaPackageRel']);
            $loaId = $clientLoaPackageRel['loaId'];
        }
        $loa = $this->Loa->findByLoaId($loaId);

        return $loa['Loa']['endDate'];
    }

    public function inplace_notes_save()
    {
        $this->autoRender = false;
        $this->Loa->id = str_replace('notes-', "", $this->params['form']['editorId']);
        $this->Loa->saveField('notes', $this->params['form']['value']);
        $notesSaved = $this->Loa->read('notes');
        echo $notesSaved['Loa']['notes'];
    }

    public function prepdocument($id,$clientId)
    {
        //$this->layout= 'overlay_form';
     Configure::write('debug',0);

        if (!isset($id,$clientId)) {
            $this->Session->setFlash(__('You must select a valid LOA', true));

        }

        $client = $this->Loa->Client->findByClientId($clientId);

        $arrContactsDropdown =array();
        if (isset($client['ClientContact'])){
            foreach ($client['ClientContact'] as $cKey=>$cVal){
                $arrContactsDropDown[$cVal['name']]=$cVal['name'];
            }
        }
        $contactPrefix = array('Mr.'=>'Mr.','Ms.'=>'Ms.','Mrs.'=>'Mrs.','Dr.'=>'Dr.','Prof.'=>'Prof.');

        $this->set('loaId', $id);
        $this->set('clientId', $clientId);
        $this->set('client', $client);
        $this->set('arrContactsDropDown',$arrContactsDropDown);
        $this->set('contactPrefix',$contactPrefix);

        $salesPeople =  $this->Loa->getSalesPeople();

        if (!empty($salesPeople)){
            $salesPeopleAutoComplete =array();
            $i =0;
            foreach ($salesPeople as $username=>$fullName){
                $fullName = str_replace("\r", "", $fullName);
                $fullName = str_replace("\n", "", $fullName);
                $salesPeopleAutoComplete[]= array('value'=>$username,'label'=>addslashes($fullName));
            }
        }

        $this->set('listSalesPeople',$salesPeopleAutoComplete);

/*
        if (empty($id)) {
            $this->Session->setFlash(__('You must select a valid LOA', true));

        } else {

            if (!empty($this->data)) {

                if (is_array($this->data['Loa']['checkboxes'])) {
                    $this->data['Loa']['checkboxes'] = implode(",", $this->data['Loa']['checkboxes']);
                }
                $this->data['Loa']['modifiedBy'] = $this->user['LdapUser']['username'];

                if (empty($this->data['Loa']['sites'])) {
                    $loa = $this->Loa->find($this->data['Loa']['loaId']);
                    $this->data['Client'] = $loa['Client'];
                    $this->Session->setFlash(__('You must select at least one site to save this LOA.', true));
                }
            }
            $this->Loa->recursive = 2;
            if (empty($this->data)) {
                $this->data = $this->Loa->read(null, $id);
                if ($this->data) {
                    usort($this->data['LoaItem'], array($this, 'sortLoaItemsByType'));
                }
            }
            $this->Loa->LoaPublishingStatusRel->PublishingStatus->recursive = -1;

            $completedStatusLL = array();
            $completedStatusFG = array();
            if (!empty($this->data['LoaPublishingStatusRel'])) {
                foreach ($this->data['LoaPublishingStatusRel'] as $pStatus) {
                    if ($pStatus['site'] == 'luxurylink') {
                        $completedStatusLL[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
                    } else {
                        $completedStatusFG[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
                    }
                }
            }

            $this->set(
                compact(
                    'completedStatusLL',
                    'completedStatusFG'
                )
            );
            $client = $this->Loa->Client->findByClientId($this->data['Loa']['clientId']);
            $checkboxValuesSelectedArr = array();
            foreach ($client['Loa'] as $key => $arr) {
                if ($arr['loaId'] == $id) {
                    $checkboxValuesSelectedArr = explode(",", $arr['checkboxes']);
                }
            }
            $arrContactsDropdown =array();
            if (isset($client['ClientContact'])){

                foreach ($client['ClientContact'] as $cKey=>$cVal){

                        $arrContactsDropDown[$cVal['name']]=$cVal['name'];
                }
            }
            $this->set('client', $client);
            $this->set('clientId', $this->data['Loa']['clientId']);
           // $this->set('checkboxValuesArr', $this->getCheckboxValuesArr());
            $this->set('checkboxValuesSelectedArr', $checkboxValuesSelectedArr);
            $this->set('arrContactsDropDown',$arrContactsDropDown);


            $salesPeople =  $this->Loa->getSalesPeople();

            if (!empty($salesPeople)){

                $salesPeopleAutoComplete =array();
                $i =0;
                foreach ($salesPeople as $username=>$fullName){

                    $fullName = str_replace("\r", "", $fullName);
                    $fullName = str_replace("\n", "", $fullName);

                    $salesPeopleAutoComplete[]= array('value'=>$username,'label'=>addslashes($fullName));

                }
            }

            $this->set('listSalesPeople',$salesPeopleAutoComplete);
        }*/
    }
}
