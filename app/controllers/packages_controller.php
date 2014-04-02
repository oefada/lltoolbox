<?php

class PackagesController extends AppController
{
    public $name = 'Packages';
    var $helpers = array('Html', 'Form');
    var $uses = array('Package', 'Client', 'PackageRatePeriod', 'LoaItem', 'IdCreator', 'Loa', 'PricePoint');
    var $paginate = array('order' => array('Package.packageId' => 'desc'));
    // Where to send errors 
    const DEV_EMAIL = 'devmail@luxurylink.com';

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->set('currentTab', 'property');
        $this->set('searchController', 'client');
        $this->set('currentTab', 'property');
        $this->set('searchController', 'client');
        if (isset($this->params['clientId'])) {
            $this->set('client', $this->Client->findByClientId($this->params['clientId']));
            $this->set('clientId', $this->params['clientId']);
        }
    }

    function clonePackageAcrossLoas($clientId, $packageId)
    {
        if (isset($this->params['url']['loa'])) {
            $newPackageId = $this->Package->clonePackageToDifferentLoa($packageId, $this->params['url']['loa']);
            if ($newPackageId && is_numeric($newPackageId)) {
                $this->redirect('/clients/' . $clientId . '/packages/summary/' . $newPackageId);
            }
        }

        $package = $this->Package->findByPackageId($packageId);
        $loas = $this->Loa->getLoaOptionList($clientId);
        foreach ($package['ClientLoaPackageRel'] as $clpr) {
            // can't clone package to an LOA it's already tied to
            if (isset($loas[$clpr['loaId']])) {
                unset($loas[$clpr['loaId']]);
            }

            // client from LOA must match URL
            if ($clpr['clientId'] != $clientId) {
                unset($loas[$clpr['loaId']]);
            }
        }
        $this->set('client', $this->Client->findByClientId($clientId));
        $this->set('package', $package);
        $this->set('loas', $loas);
    }

    function index($clientId = null)
    {
        if (!isset($clientId) && !isset($this->params['named']['clientId'])) {
            $this->cakeError('error404');
        }

        if (isset($this->params['named']['clientId']) && $clientId == null) {
            $clientId = $this->params['named']['clientId'];
        } else {
            $this->params['named']['clientId'] = $clientId;
        }
        $packages = $this->paginate('ClientLoaPackageRel', array('ClientLoaPackageRel.clientId' => $clientId));
        $packageIds = array();
        foreach ($packages as $package) {
            if (isset($package['Package']['packageId'])) {
                $pid = $package['Package']['packageId'];
                $packageIds[$pid] = $pid;
            }
        }
        $pricePoints = $this->PricePoint->find(
            'all',
            array(
                'conditions' => array(
                    "PricePoint.packageId" => $packageIds
                )
            )
        );
        $validityEnds = array();
        foreach ($pricePoints as $pp) {
            if (isset($pp['PricePoint']['packageId']) && isset($pp['PricePoint']['validityEnd'])) {
                $ppPackageId = $pp['PricePoint']['packageId'];
                $ppValidityEnd = $pp['PricePoint']['validityEnd'];
                if (!isset($validityEnds[$ppPackageId])) {
                    $validityEnds[$ppPackageId] = $ppValidityEnd;
                } else {
                    if ($validityEnds[$ppPackageId] < $ppValidityEnd) {
                        $validityEnds[$ppPackageId] = $ppValidityEnd;
                    }
                }
            }
        }
        foreach ($packages as $k => $v) {
            if (isset($v['Package']['packageId'])) {
                $packages[$k]['Package']['isFamily'] = $this->Package->isFamilyByPackageId($v['Package']['packageId']);
                if (isset($validityEnds[$v['Package']['packageId']])) {
                    $packages[$k]['Package']['lastPricePointValidityEnd'] = $validityEnds[$v['Package']['packageId']];
                }
            }
        }
        $this->set('packages', $packages);
        $this->set('packageStatusIds', $this->Package->PackageStatus->find('list'));
        $this->set(
            'client',
            $this->Client->find(
                'first',
                array('conditions' => array('Client.clientId' => $clientId), 'contain' => array('ClientContact'))
            )
        );
        $this->set('clientId', $clientId);
    }

    function carveRatePeriods($clientId = null, $id = null)
    {
        if (empty($this->data['Package']['CheckedLoaItems'])) {
            return;
        }
        $this->Package->recursive = 2;

        $packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
        $packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];

        $carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods(
            $this->data['Package']['CheckedLoaItems'],
            $this->data['PackageLoaItemRel'],
            $packageStartDate,
            $packageEndDate
        );
        if (isset($carvedRatePeriods['PackageRatePeriod'])) {
            $this->data['PackageRatePeriod'] = $carvedRatePeriods['PackageRatePeriod'];
        }
    }

    function getCarvedRatePeriods($clientId = null, $id = null)
    {
        if (empty($this->data['Package']['CheckedLoaItems'])) {
            return;
        }
        $this->Package->recursive = 2;

        $packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
        $packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];

        $carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods(
            $this->data['Package']['CheckedLoaItems'],
            $this->data['PackageLoaItemRel'],
            $packageStartDate,
            $packageEndDate
        );
        return $carvedRatePeriods;
    }

    function carveRatePeriodsForDisplay()
    {
        $this->autoRender = false;

        // set recursive to 2 so we can access all the package loa item relations also
        $this->Package->recursive = 2;

        // retrieve all loa items related to this package id
        $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
        $this->set('currencyCodes', $currencyCodes);

        $packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
        $packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];

        $carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods(
            $this->data['Package']['CheckedLoaItems'],
            $this->data['PackageLoaItemRel'],
            $packageStartDate,
            $packageEndDate
        );
        $this->set('packageRatePeriods', $carvedRatePeriods);
        $this->set('packageRatePreview', true);
        $this->render(null, null, 'package_rate_periods');
    }

    function add($clientId = null)
    {

        if (!empty($this->data)) {
            if ($this->data['Package']['packageNewOfferType'] == '0') {
                $this->redirect(
                    '/clients/' . $clientId . '/packages/edit_package/0?loaId=' . $this->data['ClientLoaPackageRel'][0]['loaId'] . '&packageNewOfferType=standard&siteId=' . $this->data['Package']['siteId']
                );
                return;
            } elseif ($this->data['Package']['packageNewOfferType'] == '2') {
                if (!empty($this->data['Package']['siteId'])) {
                    switch ($this->data['Package']['siteId']) {
                        case 1 :
                            $this->data['Package']['sites'] = array('luxurylink');
                            break;
                        case 2 :
                            $this->data['Package']['sites'] = array('family');
                            break;
                        default :
                            break;
                    }
                }
                $this->Package->create();
                if ($this->Package->saveAll($this->data)) {
                    $packageId = $this->Package->getLastInsertID();
                    $this->redirect('/clients/' . $clientId . '/packages/summary/' . $packageId);
                } else {
                    $this->Session->setFlash('This multi-client package could not be saved.');
                }
            }
        }

        $packageAttributes = $this->Package->PackageType->find('list',array('fields'=>array('packageTypeId','name')));
        $this->set(compact('packageAttributes'));

        $this->set('clientId', $clientId);
        $this->set('currentTab', 'property');

        // for hotel offers
        if ($this->data['Package']['externalOfferUrl']) {

            $this->data['Package']['packageName'] = $this->data['Package']['packageTitle'];
            $this->data['Package']['packageStatusId'] = 4;
            if ($this->Package->saveAll($this->data, array('validate' => false)) && $this->Package->save(
                    $this->data,
                    array('validate' => false)
                )
            ) {
                //create price point
                $pricePointId = $this->Package->PricePoint->createHotelOfferPricePoint($this->Package->id);
                // create schedulingMaster
                $this->Package->SchedulingMaster->create();
                $sched_master['SchedulingMaster']['packageId'] = $this->Package->id;
                $sched_master['SchedulingMaster']['pricePointId'] = $pricePointId;
                $sched_master['SchedulingMaster']['offerTypeId'] = 7;
                $sched_master['SchedulingMaster']['iterationSchedulingOption'] = 1;
                $sched_master['SchedulingMaster']['remittanceTypeId'] = 0;
                $sched_master['SchedulingMaster']['mysteryIncludes'] = '';
                $sched_master['SchedulingMaster']['startDate'] = $this->data['Package']['startDate'];
                $sched_master['SchedulingMaster']['endDate'] = $this->data['Package']['endDate'];
                $sched_master['SchedulingMaster']['siteId'] = $this->data['Package']['siteId'];
                // create schedulingInstance
                if ($this->Package->SchedulingMaster->saveAll($sched_master, array('validate' => false))) {
                    $instanceData['SchedulingInstance']['schedulingMasterId'] = $this->Package->SchedulingMaster->id;
                    $instanceData['SchedulingInstance']['startDate'] = $this->data['Package']['startDate'];
                    $instanceData['SchedulingInstance']['endDate'] = $this->data['Package']['endDate'];
                    $this->Package->SchedulingMaster->SchedulingInstance->create();
                    $this->Package->SchedulingMaster->SchedulingInstance->save($instanceData);
                } else {
                    $this->Session->setFlash(
                        __('The Schedule could not be saved. Please correct the errors below.', true),
                        'default',
                        array(),
                        'error'
                    );
                }
                //push tracking links out to front end databases
                // does this work? I think save() should get $tracking passed to it and not $data
                if (!empty($this->data['ClientTracking'])) {
                    $sites = $this->Package->field('sites', array('Package.packageId' => $this->Package->id));
                    $sites = explode(',', $sites);
                    foreach ($sites as $site) {
                        foreach ($this->data['ClientTracking'] as $tracking) {
                            $data['ClientTracking'] = $tracking;
                            $data['ClientTracking']['packageId'] = $this->Package->id;
                            $this->Package->ClientTracking->useDbConfig = $site;
                            $this->Package->ClientTracking->create();
                            $this->Package->ClientTracking->save($data);
                            $this->Package->ClientTracking->useDbConfig = 'default';
                        }
                    }
                }
                $this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
                $this->redirect("/clients/$clientId/packages/edit/{$this->Package->id}");
            } else {
                $this->Session->setFlash(
                    __('The Package could not be saved. Please correct the errors below and try again.', true),
                    'default',
                    array(),
                    'error'
                );
            }
            return;
        }

        if (!empty($this->data) && isset($this->data['Package']['complete'])) {
            $this->data = $this->setCorrectNumNights($this->data);
            $this->addPackageLoaItems();
            $this->getBlackoutDaysNumber();
            $this->carveRatePeriods($clientId);
            if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
                $this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
                $this->redirect("/clients/$clientId/packages/edit/{$this->Package->id}");
            } else {
                $this->Session->setFlash(
                    __('The Package could not be saved. Please correct the errors below and try again.', true),
                    'default',
                    array(),
                    'error'
                );
            }
        }

        $client = $this->Client->findByClientId($clientId);
        $this->set('client', $client);

        $formats = $this->Package->Format->find('list');
        $this->set('formats', $formats);

        $packageStatusIds = $this->Package->PackageStatus->find('list');
        $this->set('packageStatusIds', ($packageStatusIds));

        $currencyIds = $this->Package->Currency->find('list');
        $this->set('currencyIds', ($currencyIds));

        if (empty($this->data)) {
            $clients[0] = $this->Client->findByClientId($clientId);
            $loaIds = $this->Package->ClientLoaPackageRel->Loa->getLoaOptionList($clientId);
            if (empty($loaIds) && !empty($clients[0]['Client']['parentClientId'])) {
                $loaIds = $this->Package->ClientLoaPackageRel->Loa->getLoaOptionList(
                    $clients[0]['Client']['parentClientId']
                );
            }
            $loaIds = array($loaIds);
            $this->set('clients', $clients);
            $this->set('loaIds', $loaIds);
            $this->render('add_step_1');
            return;
        }

        $percentSum = 0;
        $loaIds = array();
        //need to reset the array declared before this if/else

        $this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());

        if (count($this->data['ClientLoaPackageRel']) == 1) {
            $this->data['ClientLoaPackageRel'][0]['percentOfRevenue'] = '100';
        }

        foreach ($this->data['ClientLoaPackageRel'] as $clientLoaPackageRel) :
            $percentSum += $clientLoaPackageRel['percentOfRevenue'];
        endforeach;

        //if the percentages don't add up to 100%, re-display the first form
        if (100 != $percentSum) :
            $this->Session->setFlash("Total percent of revenue ({$percentSum}%) must add up to 100%");
            $this->set('clients', $clients);
            $this->set(compact('loaIds'));
            $this->render('add_step_1');
        endif;

        //this re-numbers the array so we have a continuous array, since people can add/remove items on the list
        $this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());
        $this->Client->Loa->recursive = 2;
        foreach ($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel) :
            $loa = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);
            $track = $this->Client->Loa->Track->findByTrackId($clientLoaPackageRel['trackId']);
            $clientLoaDetails[$key] = $loa;
            $clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
            $clientLoaDetails[$key]['ClientLoaPackageRel']['Track'] = $track['Track'];

            // ticket 2263
            $this->data['Package']['endDate'] = $loa['Loa']['endDate'];

        endforeach;

        $this->set('clientLoaDetails', $clientLoaDetails);
        $this->data['Currency'] = $clientLoaDetails[0]['Currency'];
        $this->data['Package']['currencyId'] = $clientLoaDetails[0]['Currency']['currencyId'];
        $this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));

        $loaItemTypes = $this->Package->PackageLoaItemRel->LoaItem->LoaItemType->find('list');
        $trackExpirationCriteriaIds = $this->Package->ClientLoaPackageRel->Loa->Track->ExpirationCriterium->find(
            'list'
        );
        $familyAmenities = $this->Package->FamilyAmenity->find('list');
        $this->set(compact('loaItemTypes', 'trackExpirationCriteriaIds', 'familyAmenities'));
    }

    function getBlackoutDaysNumber($reverse = 0)
    {
        if ($reverse == 0) {
            $days = $this->data['Package']['Recurring Day Blackout'];

            if (empty($days)) {
                unset($this->data['Package']['blackoutDays']);
                return;
            }

            $this->data['Package']['blackoutDays'] = implode(',', $days);

            $blackoutDays = $this->_createBlackoutsBasedOnDays($days);

            if (isset($this->data['PackageValidityPeriod']) && is_array($this->data['PackageValidityPeriod'])) {
                $this->data['PackageValidityPeriod'] = array_merge($this->data['PackageValidityPeriod'], $blackoutDays);
            } else {
                $this->data['PackageValidityPeriod'] = $blackoutDays;
            }
        } else {
            $days = $this->data['Package']['blackoutDays'];
            $this->data['Package']['Recurring Day Blackout'] = explode(',', $days);
        }
    }

    function _createBlackoutsBasedOnDays($selectedDays)
    {
        $validityStartDate = is_array($this->data['Package']['validityStartDate']) ? implode(
            '/',
            $this->data['Package']['validityStartDate']
        ) : $this->data['Package']['validityStartDate'];
        $validityEndDate = is_array($this->data['Package']['validityEndDate']) ? implode(
            '/',
            $this->data['Package']['validityEndDate']
        ) : $this->data['Package']['validityEndDate'];

        $weekDays = array(1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

        $seedDate = strtotime($validityStartDate);

        $dayOfStartDate = date('N', $seedDate);

        $isWeekDayRepeat = 1;
        if (in_array($dayOfStartDate, $selectedDays)) {
            $startDate = date('Y-m-d', $seedDate);
            $endDate = $startDate;
            $blackoutDays[] = compact('startDate', 'endDate', 'isWeekDayRepeat');
        }

        $timeStampEndDate = strtotime($validityEndDate);
        while ($seedDate <= $timeStampEndDate) :
            foreach ($selectedDays as $day) :
                $blackoutDay = strtotime("next {$weekDays[$day]}", $seedDate);

                if ($blackoutDay < $timeStampEndDate) {
                    $startDate = date('Y-m-d', $blackoutDay);
                    $endDate = $startDate;
                    $blackoutDays[] = compact('startDate', 'endDate', 'isWeekDayRepeat');
                }
            endforeach;

            $seedDate = strtotime('next week', $seedDate);
        endwhile;

        return $blackoutDays;
    }

    /**
     * Function is called from {@link add()} to link LOA Items to the package
     *
     * @see updatePackageLoaItems()
     * @todo consolidate with {@link updatePackageLoaItems}
     */
    function addPackageLoaItems()
    {
        if (!isset($this->data['PackageLoaItemRel'])) {
            return true;
        }

        $origPackageLoaItemRel = $this->data['PackageLoaItemRel'];
        unset($this->data['PackageLoaItemRel']);

        if (isset($this->data['Package']['CheckedLoaItems'])) :
            foreach ($this->data['Package']['CheckedLoaItems'] as $k => $checkedLoaItem) {
                $this->data['PackageLoaItemRel'][$checkedLoaItem]['quantity'] = $origPackageLoaItemRel[$checkedLoaItem]['quantity'];
                $this->data['PackageLoaItemRel'][$checkedLoaItem]['weight'] = $origPackageLoaItemRel[$checkedLoaItem]['weight'];
                $this->data['PackageLoaItemRel'][$checkedLoaItem]['loaItemTypeId'] = $origPackageLoaItemRel[$checkedLoaItem]['loaItemTypeId'];
                $this->data['PackageLoaItemRel'][$checkedLoaItem]['loaItemId'] = $checkedLoaItem;
            }
        endif;

        return true;
    }

    /**
     * This method is called from the view for {@link selectAdditionalClient()} after a client is selected
     * It uses the clientId passed to retrieve all of the fields needed to add a new client to step 1
     *
     * @param int $clientId
     * @author Victor Garcia
     */
    function fetchMultipleClientsFormFragment($clientId = null)
    {
        $this->set('rowId', $this->params['named']['rowId']);
        $this->set('clientId', $clientId);

        $client = $this->Client->findByClientId($clientId);
        $this->set('client', $client);

        $loaIds = $this->Package->ClientLoaPackageRel->Loa->getLoaOptionList($clientId);
        if (empty($loaIds) && !empty($clients[0]['Client']['parentClientId'])) {
            $loaIds = $this->Package->ClientLoaPackageRel->Loa->getLoaOptionList(
                $clients[0]['Client']['parentClientId']
            );
        }
        $loaIds = array($loaIds);
        $this->set('loaIds', $loaIds);
        $this->set('additionalClient', 1);
        $this->render('_add_step_1_fields');
    }

    /**
     * Method displays a modal dialog box with all the clients. Used in conjunction with {@link
     * fetchMultipleClientsFormFragment()}
     *
     * @author Victor Garcia
     */
    function selectAdditionalClient()
    {
        $this->set('data', $this->data);
        if (isset($this->data['search'])) :
            $searchTerm = strtolower($this->data['search']);
            if ($clients = $this->Client->searchClients($searchTerm)) {
                $this->set('clients', $clients);
            } else {
                $this->set('clients', array());
            }
            $this->set('showPagination', 0); else :
            $this->set('clients', $this->paginate('Client'));
            $this->set('showPagination', 1);
        endif;
    }

    /**
     * Method works just like {@link addPackageLoaItems()} but for existing packages. It is called from {@link edit()}
     * It goes through all checked items and updates quantities or removes them from the relationship as needed
     *
     * @see addPackageLoaItems()
     * @todo consolidate with {@link addPackageLoaItems}
     */
    function updatePackageLoaItems()
    {
        //grab the new quantities from the form, the data array looks like the one from the databases but with only the quantity
        // field
        $currentItemIds = array();
        $newPackageLoaItemRel = @$this->data['PackageLoaItemRel'];

        unset($this->data['PackageLoaItemRel']);

        //set the PackageLoaItemRel array to the arrays stored in this package
        $this->Package->PackageLoaItemRel->recursive = -1;
        $packageLoaItemRelations = $this->Package->PackageLoaItemRel->find(
            'all',
            array('conditions' => array('PackageLoaItemRel.packageId' => $this->data['Package']['packageId']))
        );

        //loop through all of the loa items associated to this package
        if (!isset($this->data['Package']['CheckedLoaItems'])) {
            $this->Package->PackageLoaItemRel->deleteAll(
                array('PackageLoaItemRel.packageId' => $this->data['Package']['packageId'], true)
            );
            return true;
        }

        foreach ($packageLoaItemRelations as $k => &$packageLoaItemRel) :
            $packageLoaItemRel = $packageLoaItemRel['PackageLoaItemRel'];
            //delete all of the items that are no longer associated with this package
            if (!in_array($packageLoaItemRel['loaItemId'], $this->data['Package']['CheckedLoaItems'])) {
                if ($this->Package->PackageLoaItemRel->delete($packageLoaItemRel['packageLoaItemRelId'])) {
                    unset($this->data['PackageLoaItemRel'][$k]);
                    //unset the array so when we don't re-save this
                }
            } else { //if the new quantity is different from the old, update the field
                $currentItemIds[] = $packageLoaItemRel['loaItemId'];
                $packageLoaItemRel['quantity'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['quantity'];
                $packageLoaItemRel['weight'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['weight'];
                $packageLoaItemRel['loaItemTypeId'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['loaItemTypeId'];
                $this->data['PackageLoaItemRel'][] = $packageLoaItemRel;
            }
        endforeach;

        //here we deal with the new items
        if (isset($this->data['Package']['CheckedLoaItems'])) :
            foreach ($this->data['Package']['CheckedLoaItems'] as $k => $checkedLoaItem) {
                if (!in_array($checkedLoaItem, $currentItemIds)) :
                    $newPackageLoaItems[$k]['quantity'] = $newPackageLoaItemRel[$checkedLoaItem]['quantity'];
                    $newPackageLoaItems[$k]['weight'] = $newPackageLoaItemRel[$checkedLoaItem]['weight'];
                    $newPackageLoaItems[$k]['loaItemTypeId'] = $newPackageLoaItemRel[$checkedLoaItem]['loaItemTypeId'];
                    $newPackageLoaItems[$k]['loaItemId'] = $checkedLoaItem;
                    $newPackageLoaItems[$k]['packageId'] = $this->data['Package']['packageId'];
                endif;
            }
        endif;
        if (isset($this->data['PackageLoaItemRel']) && is_array(
                $this->data['PackageLoaItemRel']
            ) && isset($newPackageLoaItems)
        ) :
            $this->data['PackageLoaItemRel'] = array_merge_recursive(
                $this->data['PackageLoaItemRel'],
                $newPackageLoaItems
            ); elseif (isset($newPackageLoaItems)) :
            $this->data['PackageLoaItemRel'] = $newPackageLoaItems;
        endif;

        $this->setUpPackageLoaItemRelArray();
        return true;
    }

    function setUpPackageLoaItemRelArray()
    {
        $tmp = array();
        if (isset($this->data['PackageLoaItemRel'])) :
            foreach ($this->data['PackageLoaItemRel'] as $v) {
                $tmp[$v['loaItemId']] = $v;
            }

            $this->data['PackageLoaItemRel'] = $tmp;

        endif;
    }

    function edit($clientId = null, $id = null)
    {
        if (!$clientId && !$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Package or Client', true));
            $this->redirect(array('controller' => 'clients', 'action' => 'index'));
        }

        if (!empty($this->data)) {

            if (!empty($this->data['Package']['externalOfferUrl'])) { // for hotel offers

                //push tracking links out to front end databases
                if (!empty($this->data['ClientTracking'])) {

                    $sites = $this->Package->field('sites', array('Package.packageId' => $id));
                    $sites = explode(',', $sites);
                    foreach ($sites as $site) {
                        foreach ($this->data['ClientTracking'] as $key => $tracking) {
                            $data['ClientTracking'] = $tracking;
                            $data['ClientTracking']['packageId'] = $id;
                            $this->Package->ClientTracking->useDbConfig = $site;
                            $this->Package->ClientTracking->create();
                            $this->Package->ClientTracking->save($tracking);
                            $this->Package->ClientTracking->useDbConfig = 'default';
                        }
                    }
                }

                $this->data['Package']['packageName'] = $this->data['Package']['packageTitle'];

                $arr = array('validate' => false);
                if ($this->Package->saveAll($this->data, $arr) && $this->Package->save($this->data, $arr)) {
                    $this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
                    $this->redirect("/clients/$clientId/packages/edit/" . $this->Package->id);

                } else {

                    $msg = 'The Package could not be saved. Please correct the errors below and try again.';
                    $this->Session->setFlash(__($msg, true), 'default', array(), 'error');

                }

                return;

            }

            if (@$this->data['clone'] == 'clone') {
                $this->data = $this->Package->cloneData($this->data);
                $this->addPackageLoaItems();
                $cloned = true;
            } else {
                $this->Package->PackageRatePeriod->deleteAll(
                    array('PackageRatePeriod.packageId' => $this->data['Package']['packageId'])
                );
                $this->updatePackageLoaItems();
                $cloned = false;
            }

            $this->data = $this->setCorrectNumNights($this->data);
            $this->getBlackoutDaysNumber();
            $this->carveRatePeriods($clientId);
            //remove all offer type defaults so we don't get duplicates
            $this->Package->PackageOfferTypeDefField->deleteAll(
                array('PackageOfferTypeDefField.packageId' => $this->data['Package']['packageId']),
                false
            );

            //remove all recurring days so we don't get duplicates
            $this->Package->PackageValidityPeriod->deleteAll(
                array('PackageValidityPeriod.packageId' => $this->data['Package']['packageId'], 'isWeekDayRepeat' => 1),
                false
            );

            $this->Package->PackageAgeRange->deleteAll(
                array('PackageAgeRange.packageId' => $this->data['Package']['packageId']),
                false
            );

            if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
                if (true == $cloned) {
                    $this->Session->setFlash(
                        __('Package was cloned from package #' . $this->data['Package']['copiedFromPackageId'], true),
                        'default',
                        array(),
                        'success'
                    );
                } else {
                    $this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
                }
                $this->redirect("/clients/$clientId/packages/edit/" . $this->Package->id);
            } else {
                $this->Session->setFlash(
                    __('The Package could not be saved. Please correct the errors below and try again.', true),
                    'default',
                    array(),
                    'error'
                );
            }
        }

        if (empty($this->data)) {
            $package = $this->Package->read(null, $id);
            $this->data = $package;
        } else {
            $package = $this->data;
        }

        switch ($package['Package']['siteId']) {
            case 2 :
                $this->set('siteUrl', 'www.familygetaway.com');
                break;
            case 1 :
            default :
                $this->set('siteUrl', 'www.luxurylink.com');
        }

        $client_trackings = array();

        // map clientTracking: use clientTrackingTypeId as key
        if (isset($this->data['ClientTracking'])) {
            foreach ($this->data['ClientTracking'] as $k => $v) {
                $client_trackings[$v['clientTrackingTypeId']] = $v;
            }
        }
        $this->data['ClientTracking'] = $client_trackings;

        usort($package['PackageAgeRange'], array($this, 'sortPackageAgeRange'));
        //sort age ranges

        $this->set('package', $package);
        $this->getBlackoutDaysNumber(1);
        $this->Package->ClientLoaPackageRel->recursive = -1;
        $clientLoaPackageRel = $this->Package->ClientLoaPackageRel->findAllByPackageId($id);
        $this->LoaItem->recursive = 2;
        $this->LoaItem->Behaviors->attach('Containable');

        $clientLoaDetails = array();
        foreach ($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel) {
            $clientLoaDetails[$key] = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);

            $clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;

            //Get all the fees for each item
            if (isset($clientLoaDetails[$key]['LoaItem'])) {
                foreach ($clientLoaDetails[$key]['LoaItem'] as $k => $v) {
                    $itemId = $v['loaItemId'];
                    $loaItem = $this->LoaItem->read(null, $itemId);
                    $clientLoaDetails[$key]['LoaItem'][$k]['Fee'] = $loaItem['Fee'];
                    $clientLoaDetails[$key]['LoaItem'][$k]['LoaItemRatePeriod'] = $loaItem['LoaItemRatePeriod'];
                }
            }
        }

        $formats = $this->Package->Format->find('list');
        $this->set('formats', $formats);

        $packageStatusIds = $this->Package->PackageStatus->find('list');
        $this->set('packageStatusIds', ($packageStatusIds));

        $currencyIds = $this->Package->Currency->find('list');
        $this->set('currencyIds', ($currencyIds));

        $packageLoaItems = $this->Package->PackageLoaItemRel->findAllByPackageId($this->data['Package']['packageId']);

        foreach ($packageLoaItems as $k => $v) :
            $this->data['Package']['CheckedLoaItems'][] = $v['PackageLoaItemRel']['loaItemId'];
        endforeach;

        //sort the LOA Items so that the checked ones appear at the top
        $approvalNotNeeded = 0;
        foreach ($clientLoaDetails as $k => $a) :
            if (isset($clientLoaDetails[$k]['LoaItem'])) {
                uasort($clientLoaDetails[$k]['LoaItem'], array($this, 'sortLoaItemsForEdit'));
            }

            $track = $this->Package->ClientLoaPackageRel->Loa->Track->findByTrackId(
                $a['ClientLoaPackageRel']['trackId']
            );

            //Check if the track is a Keep track. Internal approval only needed for keep tracks
            if (empty($track['Track']['applyToMembershipBal']) && isset($approvalNotNeeded)) {
                $approvalNotNeeded = 1;
            } else {
                unset($approvalNotNeeded);
            }

            $clientLoaDetails[$k]['ClientLoaPackageRel']['Track'] = $track['Track'];
        endforeach;

        if (isset($approvalNotNeeded) && $approvalNotNeeded == 1) {
            $this->data['Package']['internalApproval'] = $approvalNotNeeded;
        }

        $this->set('clientLoaDetails', $clientLoaDetails);

        $client = $this->Client->findByClientId($clientId);
        $this->set('client', $client);

        $this->set('clientId', $clientId);
        $this->set('packageId', $this->Package->id);

        $this->setUpPackageLoaItemRelArray();
        $itemList = $this->Package->PackageLoaItemRel->LoaItem->find('list');
        //$itemCurrencyIds = $this->Package->PackageLoaItemRel->LoaItem->Loa->find('list', array('fields' => array('currencyId'),
        // 'conditions' => array('Loa.loaId' => $this->data['ClientLoaPackageRel'][0]['loaId'])));

        $carvedRatePeriods = $this->getCarvedRatePeriods($clientId);

        if (isset($carvedRatePeriods['Boundaries']) && !empty($carvedRatePeriods['Boundaries'])) :
            //sort and re-set keys for the boundaries array
            sort($carvedRatePeriods['Boundaries']);
            array_merge($carvedRatePeriods['Boundaries'], array());

            $packageRatePeriods = array();
            $packageRatePeriods['IncludedItems'] = $carvedRatePeriods['IncludedItems'];
            $packageRatePeriods['Boundaries'] = $carvedRatePeriods['Boundaries'];
            $this->set('packageRatePeriods', $packageRatePeriods);
        endif;

        $this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));

        if (!isset($this->data['Format']['Format'])) :
            $formatList = array();
            foreach ($this->data['Format'] as $format) :
                $formatList[] = $format['formatId'];
            endforeach;

            $this->data['Format']['Format'] = $formatList;
        endif;

        $this->setupOfferTypeDefArray();

        $familyAmenities = $this->Package->FamilyAmenity->find('list');
        $loaItemTypes = $this->Package->PackageLoaItemRel->LoaItem->LoaItemType->find('list');
        $trackExpirationCriteriaIds = $this->Package->ClientLoaPackageRel->Loa->Track->ExpirationCriterium->find(
            'list'
        );

        // get roomGradeName for this package
        $this->Package->PackageLoaItemRel->recursive = 2;
        $loaItems = $this->Package->PackageLoaItemRel->find(
            'first',
            array('conditions' => array('Package.packageId' => $id, 'LoaItem.loaItemTypeId' => 1))
        );
        $roomGradeName = $loaItems['LoaItem']['RoomGrade']['roomGradeName'];

        $this->set(compact('loaItemTypes', 'trackExpirationCriteriaIds', 'familyAmenities', 'roomGradeName'));
    }

    function preview($clientId = null, $id = null)
    {
        $this->Package->Behaviors->attach('Containable');
        $this->Package->contain(array('PackageLoaItemRel', 'ClientLoaPackageRel', 'Currency'));

        $package = $this->Package->read(null, $id);

        foreach ($package['ClientLoaPackageRel'] as $clientRel) {
            if ($clientId == $clientRel['clientId']) {
                $this->Client->recursive = -1;
                $client = $this->Client->read(null, $clientId);
                $package['Client'] = $client['Client'];
                break;
            }
        }

        if (!isset($client)) {
            $this->cakeError('error404');
        }

        $this->Package->PackageLoaItemRel->LoaItem->Behaviors->attach('Containable');
        $this->Package->PackageLoaItemRel->LoaItem->contain(array('Fee', 'LoaItemRatePeriod'));

        foreach ($package['PackageLoaItemRel'] as $packageLoaItemRel) {
            $items[$packageLoaItemRel['loaItemId']] = $this->Package->PackageLoaItemRel->LoaItem->read(
                null,
                $packageLoaItemRel['loaItemId']
            );
        }

        foreach ($items as $item) {
            if ($item['LoaItem']['loaItemTypeId'] != 1) {
                continue;
            }
            $ratePeriods[$item['LoaItem']['loaItemId']] = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods(
                array($item['LoaItem']['loaItemId']),
                array(),
                $package['Package']['startDate'],
                $package['Package']['endDate']
            );
        }

        $this->set(compact('package', 'items', 'ratePeriods'));

        //Fix with the routes... this isn't being automatically pulled
        if ($this->RequestHandler->prefers('doc')) {
            $this->layout = 'doc/default';
            $this->render('doc/preview');
        }
    }

    function clonePackage($clientId = null, $id = null)
    {
        $this->Package->clonePackage($id);
    }

    function setCorrectNumNights($data)
    {
        //set correct number of nights for single product package
        if (count($data['ClientLoaPackageRel']) == 1) {
            $data['ClientLoaPackageRel'][0]['numNights'] = $data['Package']['numNights'];
        }
        if (count($data['ClientLoaPackageRel'] > 1)) {
            $numNights = 0;
            foreach ($data['ClientLoaPackageRel'] as $v) {
                $numNights += $v['numNights'];
            }
            $data['Package']['numNights'] = $numNights;
        }
        return $data;
    }

    function sortPackageAgeRange($a, $b)
    {
        if ($a['rangeLow'] == $b['rangeLow']) {
            return 0;
        }

        return $a['rangeLow'] < $b['rangeLow'] ? -1 : 1;
    }

    function setupOfferTypeDefArray()
    {
        if (empty($this->data['PackageOfferTypeDefField'])) {
            return;
        }
        foreach ($this->data['PackageOfferTypeDefField'] as $defField) :
            $defFieldViewArray[$defField['offerTypeId']] = $defField;

            if (in_array(
                    $defField['offerTypeId'],
                    array(1, 2, 6)
                ) && $this->data['Package']['approvedRetailPrice'] != 0
            ) {
                $defFieldViewArray[$defField['offerTypeId']]['percentRetail'] = round(
                    $defField['openingBid'] / $this->data['Package']['approvedRetailPrice'] * 100,
                    2
                );
            } elseif ($this->data['Package']['approvedRetailPrice'] != 0) {
                $defFieldViewArray[$defField['offerTypeId']]['percentRetail'] = round(
                    $defField['buyNowPrice'] / $this->data['Package']['approvedRetailPrice'] * 100,
                    2
                );
            }
        endforeach;
        unset($this->data['PackageOfferTypeDefField']);
        $this->data['PackageOfferTypeDefField'] = $defFieldViewArray;
    }

    function sortLoaItemsForEdit($a, $b)
    {
        if (!isset($this->data['Package']['CheckedLoaItems'])) {
            return;
        }
        return in_array($b['loaItemId'], $this->data['Package']['CheckedLoaItems']);
    }

    // pacakge is actually set to inactive=1 and not deleted
    // 2011-02-15 mbyrnes
    function deletePackage()
    {

        $ppid = $this->params['pass'][1];
        $cid = $this->params['pass'][3];
        $pid = $this->params['pass'][5];
        $this->Package->PricePoint->setInactive(1, $ppid);
        $this->Session->setFlash(__('Deleted!', true));
        $this->redirect("/clients/$cid/packages/summary/$pid");

    }

    function deleteMultiplePricePoints()
    {
        //allow you to act on these via url as svc
        $strPricePoints = $this->params['pass'][1];
        $clientId = $this->params['pass'][3];
        $packageId = $this->params['pass'][5];

        if(empty($strPricePoints)){
            $this->Session->setFlash(__('Invalid Price Points', true));
            //redirect to self
            $this->redirect("/clients/$clientId/packages/summary/$packageId");
        }else{
            $pricePointsArray = explode(",",$strPricePoints);

            foreach ($pricePointsArray as $pPid){
                $this->Package->PricePoint->setInactive(1, $pPid);

            }
            $this->Session->setFlash(__('All of the PricePoints have been Deleted', true));
            //redirect to self
            $this->redirect("/clients/$clientId/packages/summary/$packageId");
        }



    }

    function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Package', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Package->del($id)) {
            $this->Session->setFlash(__('Package deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }

    function addBlackoutPeriodRow()
    {
        $this->autoRender = false;
        $this->data['PackageValidityPeriod'][] = array();

        $this->render('_step_3_blackout_periods');
    }

    function removeBlackoutPeriodRow($row)
    {
        $this->autoRender = false;

        if ($row == 'all') {
            unset($this->data['PackageValidityPeriod']);
        } else {
            if (isset($this->data['PackageValidityPeriod'][$row]['packageValidityPeriodId'])) {
                $this->Package->PackageValidityPeriod->delete($this->data['PackageValidityPeriod'][$row]);
            }
            unset($this->data['PackageValidityPeriod'][$row]);
        }

        $this->data['PackageValidityPeriod'] = array_merge($this->data['PackageValidityPeriod'], array());

        $this->render('_step_3_blackout_periods');
    }

    function getOfferTypeDefaultsHtmlFragment($packageId = null)
    {
        $this->autoRender = false;
        $formatId = $this->data['Format']['Format'][0];
        $this->Package->PackageOfferTypeDefField->recursive = -1;

        $this->setupOfferTypeDefArray();

        if (!empty($formatId)) :
            $this->render(null, null, "format_defaults_$formatId"); else :
            return '';
        endif;
    }

    function send_for_merch_approval($clientId, $packageId)
    {
        $clientData = $this->Client->findByClientId($clientId);
        $this->set('client', $clientData);
        $this->set('clientId', $clientId);
        $this->set('packageId', $packageId);

        if (!empty($this->data)) {
            $subject = $clientData['Client']['name'] . ' - Package awaiting approval';

            $body = "The following package was submitted for approval by " . $this->user['LdapUser']['displayname'] . ": ";
            $body .= "http://toolbox.luxurylink.com/clients/$clientId/packages/summary/$packageId";
            $body .= "\n\nAdditional Message: ";
            $body .= $this->data['additionalMessage'];

            $headers = "Reply-To: {$this->user['LdapUser']['mail']}\n";
            $headers .= "From: {$this->user['LdapUser']['mail']}\n";
            $headers .= "CC: {$this->user['LdapUser']['mail']}";

            if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
                $emailTo = 'devmail@luxurylink.com';
            } else {
                $emailTo = 'production@luxurylink.com';
            }

            if (mail($emailTo, $subject, $body, $headers)) {
                //set package status to Pending Publishing
                $this->Package->recursive = -1;
                $package = $this->Package->find(
                    'first',
                    array('conditions' => array('Package.packageId' => $packageId))
                );
                $package['Package']['packageStatusId'] = 6;
                $this->Package->save($package);
                $this->Session->setFlash(
                    __('The Package has been submitted for production approval', true),
                    'default',
                    array(),
                    'success'
                );
            } else {
                $this->Session->setFlash(
                    __('The Package could not be sent to production for approval', true),
                    'default',
                    array(),
                    'error'
                );
            }
        }
    }

    function performanceTooltip($id)
    {
        $this->Package->PackagePerformance->recursive = -1;
        $metrics = $this->Package->PackagePerformance->find(
            'first',
            array('conditions' => array('PackagePerformance.packageId' => $id))
        );

        $this->set('metrics', $metrics['PackagePerformance']);
    }

    function tooltipNotes($id)
    {
        $this->Package->recursive = -1;
        $notes = $this->Package->find(
            'first',
            array('fields' => 'notes', 'conditions' => array('Package.packageId' => $id))
        );

        $this->set('notes', $notes['Package']['notes']);
    }

    function age_range_row()
    {
        $this->autoRender = false;
        $this->set('row', ++$this->params['url']['last']);
        $this->set('data', null);
        $this->render('_age_range_row');
    }

    /**
     * Package revamp functions
     **/

    function summary($clientId, $packageId)
    {
        $client = $this->Client->find('first', array('conditions' => array('Client.clientId' => $clientId)));
        $this->set('client', $client);
        $package = $this->Package->getPackage($packageId);
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        $this->set('isMultiClientPackage', $isMultiClientPackage);



        if (!empty($package['Package']['externalOfferUrl'])) {
            $this->redirect('/clients/' . $clientId . '/packages/edit/' . $packageId);
        }

        if (!empty($this->data)) {

            $package['Package']['notes'] = $this->data['Package']['notes'];


            //Package type checkbox processessing
            $package['PackageType']['PackageType'] = array();
            if(!empty($this->data['PackageType'])){
                foreach ($this->data['PackageType'] as $checkboxSelected){
                    if ($checkboxSelected){
                        $package['PackageType']['PackageType'][] = $checkboxSelected;
                    }
                }
            }
            if (!$this->Package->save($package)) {

                $errors = implode("<br>", $this->Package->validationErrors);
                $this->Session->setFlash('The package was not saved<br />'.$errors);

            } else {
                $this->Session->setFlash('The package was saved');
            }
            $this->redirect('/clients/' . $clientId . '/packages/summary/' . $packageId);
        }

        $packageAttributes = $this->Package->PackageType->find('list',array('fields'=>array('packageTypeId','name')));
        $this->set(compact('packageAttributes'));

        //debug($package);
        $history = $this->Package->getHistory($packageId);
        $this->set('history', $history);
        if ($roomNights = $this->LoaItem->getRoomNights($packageId, $isMultiClientPackage)) {
            if (count($roomNights[0]['LoaItems'][0]['LoaItemRate']) > 1) {
                $this->set('isDailyRates', true);
                $dailyRatesMap = array(
                    'w0' => 'Su',
                    'w1' => 'M',
                    'w2' => 'T',
                    'w3' => 'W',
                    'w4' => 'Th',
                    'w5' => 'F',
                    'w6' => 'S'
                );
                for ($i = 1; $i <= count($roomNights[0]['LoaItems'][0]['LoaItemRate']); $i++) {
                    $labelArr = array();
                    foreach ($dailyRatesMap as $field => $label) {
                        if ($roomNights[0]['LoaItems'][0]['LoaItemRate'][$i - 1]['LoaItemRate'][$field] == 1) {
                            $labelArr[] = $label;
                        }
                    }
                    $roomNights[0]['LoaItems'][0]['LoaItemRate'][$i - 1]['LoaItemRate']['rateLabel'] = implode(
                        '/',
                        $labelArr
                    );
                }
            }
            if ($package['Package']['isTaxIncluded'] == 1) {
                if ($taxes = $this->LoaItem->Fee->getFeesForRoomType(
                    $roomNights[0]['LoaItems'][0]['LoaItem']['loaItemId']
                )
                ) {
                    $taxArr = array();
                    foreach ($taxes as $tax) {
                        $taxArr[] = $tax['Fee']['feeName'];
                    }
                    $this->set('taxLabel', implode(' and ', $taxArr));
                }
            }
        }

        // blackout validity
        $pkgVbDates = $this->Package->getPkgVbDates($packageId);
        if (!empty($pkgVbDates)) {
            if (empty($pkgVbDates['BlackoutDays'])) {
                $pkgVbDates['BlackoutDays'] = array();
            }
            $this->set('validity', $pkgVbDates['ValidRanges']);
            $this->set('blackout', $pkgVbDates['BlackoutDays']);
        }

        // blackout weekday
        $bo_weekdays = $this->Package->getBlackoutWeekday($packageId);
        $bo_weekdays_arr = array();
        foreach (explode(',', $bo_weekdays) as $w) {
            $bo_weekdays_arr[] = $this->Package->pluralize($w);
        }
        $this->set('bo_weekdays', implode('<br />', $bo_weekdays_arr));

        $this->set('ratePeriods', $roomNights);
        foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
            $packageClient['Inclusions'] = $this->LoaItem->getPackageInclusions(
                $packageId,
                $packageClient['ClientLoaPackageRel']['loaId']
            );
        }

        // low price guarantees
        $lowPriceGuarantees = $this->getRatePeriodsInfo($packageId);
        $this->set('lowPriceGuarantees', $lowPriceGuarantees);

        // price points
        $pricePoints = $this->Package->PricePoint->find(
            'all',
            array('conditions' => array('PricePoint.packageId' => $packageId, 'inactive' => 0))
        );



        $this->set('pricePoints', $pricePoints);

        $ppIdArray =array();
        foreach($pricePoints as  $ppVal){
            $ppIdArray[] = $ppVal['PricePoint']['pricePointId'];
        }
        $pricePointsList = implode(",",$ppIdArray);
        $this->set('pricePointsList', $pricePointsList);

        // currency
        $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
        $this->set('currencyCodes', $currencyCodes);

        switch ($package['Package']['siteId']) {
            case 2 :
                $this->set('siteUrl', 'www.familygetaway.com');
                break;
            case 1 :
            default :
                $this->set('siteUrl', 'www.luxurylink.com');
        }
        //required for HABTM
        if (empty($this->data)) {
            $this->data = $this->Package->read(null, $packageId);
        }

        $this->set('package', $package);

        $this->set('isFamilyPackage', $this->Package->isFamilyPackage($this->data));
    }

    function edit_package($clientId, $packageId)
    {

        if (!empty($this->data)) {

            $package = $this->data;

            if ($_POST['isAjax'] == 'true') {
                $this->autoRender = false;
            }
            if ($errors = $this->validatePackage($package)) {
                if ($_POST['isAjax'] == 'true') {
                    echo json_encode($errors);
                    return;
                } else {
                    $this->Session->setFlash(implode('<br />', $errors));
                    $this->set('package', $package);
                    $this->set('packageId', $packageId);
                    $clientLoas = $this->Client->Loa->getClientLoas($clientId);
                    $statuses = $this->Package->PackageStatus->getPackageStatus();
                    $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
                    $this->set('currencyCodes', $currencyCodes);
                    $this->set('loas', $clientLoas);
                    $this->set('statuses', $statuses);
                    return;
                }
            }
            if (empty($this->data['Package']['packageId'])) {
                $this->data['Package']['packageId'] = $packageId;
            }
            if (!empty($this->data['Package']['rateDisclaimerDesc']) && !empty($this->data['Package']['rateDisclaimerDate'])) {
                $this->data['Package']['rateDisclaimer'] = "Nightly rates based on {$this->data['Package']['rateDisclaimerDesc']}, as found through booking engine {$this->data['Package']['rateDisclaimerDate']}";
            } else {
                $this->data['Package']['rateDisclaimer'] = $this->data['Package']['customRateDisclaimerText'];
                $this->data['Package']['rateDisclaimerDesc'] = null;
                $this->data['Package']['rateDisclaimerDate'] = null;
            }
            if (empty($this->data['PackageAgeRange']['packageAgeRangeId'])) {
                unset($this->data['PackageAgeRange']['packageAgeRangeId']);
            }
            if (empty($this->data['PackageAgeRange']['rangeLow']) && empty($this->data['PackageAgeRange']['rangeHigh'])) {
                unset($this->data['PackageAgeRange']['rangeLow']);
                unset($this->data['PackageAgeRange']['rangeHigh']);
            }
            unset($this->data['Package']['customRateDisclaimerText']);

            if (!empty($this->data['Package']['siteId'])) {
                switch ($this->data['Package']['siteId']) {
                    case 1 :
                        $this->data['Package']['sites'] = array('luxurylink');
                        break;
                    case 2 :
                        $this->data['Package']['sites'] = array('family');
                        break;
                    default :
                        break;
                }
            }

            if ($this->data['Package']['isFlexPackage'] !== '1'){
                $this->data['Package']['flexNumNightsMin'] = null;
                $this->data['Package']['flexNumNightsMax'] = null;
                $this->data['Package']['flexNotes'] = '';
            }
            //Package type checkbox processessing
            $this->data['PackageType']['PackageType'] = array();
            if(!empty($this->data['PackageType'])){
                foreach ($this->data['PackageType'] as $checkboxSelected){
                    if ($checkboxSelected){
                        $this->data['PackageType']['PackageType'][] = $checkboxSelected;
                    }
                }
            }
        
        	if ($this->data['Package']['pegasusPackageCodeRatePlan'] != '' && $this->data['Package']['pegasusPackageCodeRoomGrade'] != '') {
        		$this->data['Package']['pegasusPackageCode'] = $this->data['Package']['pegasusPackageCodeRatePlan'] . '-' . $this->data['Package']['pegasusPackageCodeRoomGrade'];
        	} else {
        		$this->data['Package']['pegasusPackageCode'] = null;
        	}

        	if ($this->data['Package']['pegasusRackRoomGrade'] == '') {
        		$this->data['Package']['pegasusRackRoomGrade'] = null;
        	}
        	
            if ($_POST['isAjax'] == 'true') {

                if ($this->Package->saveAll($this->data)) {
                    if (empty($package['Package']['packageId'])) {
                        $packageId = $this->Package->getLastInsertId();
                        $package['Package']['packageId'] = $packageId;
                    }
                    $isMultiClientPackage = $this->Package->ClientLoaPackageRel->isMultiClientPackage($packageId);
                    $this->Package->PackageLoaItemRel->updateInclusions($package, $isMultiClientPackage);
                    if (!empty($package['LoaItemRatePackageRel'])) {
                        $this->Package->LoaItemRatePackageRel->setNumNights(&$package);
                        $this->Package->LoaItemRatePackageRel->save($package);
                    }


                    echo "ok";
                } else {
                    echo json_encode($this->Package->validationErrors);
                }

            } else {

                if ($this->Package->saveAll($this->data)) {

                    if (empty($package['Package']['packageId'])) {

                        $packageId = $this->Package->getLastInsertId();
                        //this may vary with multiclient packages. need to add logic if using this function
                        //single client packages -- percentOfRetail will always equal 100
                        $percentOfRevenue = 100;
                        $relData = array(
                            'packageId' => $packageId,
                            'clientId' => $clientId,
                            'loaId' => $package['Package']['loaId'],
                            'numNights' => $package['Package']['numNights'],
                            'percentOfRevenue' => $percentOfRevenue
                        );
                        $this->Package->ClientLoaPackageRel->create();
                        $this->Package->ClientLoaPackageRel->save($relData);
                        $package['Package']['packageId'] = $packageId;

                        $this->Package->logHistory($clientId, $packageId, "Created");

                    }

                    if (!empty($package['LoaItemRatePackageRel'])) {
                        $this->Package->LoaItemRatePackageRel->setNumNights(&$package);
                        $this->Package->LoaItemRatePackageRel->save($package['LoaItemRatePackageRel']);
                    }

                    $this->redirect('/clients/' . $clientId . '/packages/summary/' . $packageId);

                } else {

                    //print_r($this->Package->validationErrors);exit;
                    $errors = implode("<br>", $this->Package->validationErrors);
                    $this->Session->setFlash($errors);
                    //$this->redirect('/clients/'.$clientId.'/packages/edit_package/0?loaId='.$this->data['ClientLoaPackageRel'][0]['loaId']);
                }
            }
            if (!isset($packageId)) {
                $this->set("packageId", 0);
            }
            $this->set("package", $package);
        } else {
            if ($packageId) {
                $package = $this->Package->getPackage($packageId);
            } else {
                $this->Package->ClientLoaPackageRel->Loa->recursive = -1;
                $loa = $this->Package->ClientLoaPackageRel->Loa->find(
                    'first',
                    array('conditions' => array('Loa.loaId' => $_GET['loaId']))
                );

                $package['Package'] = $this->Package->schema();
                foreach ($package['Package'] as &$field) {
                    $field = '';
                }
                $siteId = Sanitize::paranoid($_GET['siteId']);
                $package['Package']['sites'] = array($this->siteDbs[$siteId]);
                $package['Package']['siteId'] = $siteId;
                $package['Package']['packageStatusId'] = 1;
                $package['Loa'] = $loa['Loa'];
            }
            //required for HABTM
            if (empty($this->data)) {
                $this->data = $this->Package->read(null, $packageId);
            }
           // $this->data = $package;
            $this->set('package', $package);
            $this->set('packageId', $packageId);
            $clientLoas = $this->Client->Loa->getClientLoas($clientId);
            $statuses = $this->Package->PackageStatus->getPackageStatus();
            $this->set('loas', $clientLoas);
            $this->set('statuses', $statuses);

            // PKGR - currency
            $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
            $this->set('currencyCodes', $currencyCodes);
        }

        $packageAttributes = $this->Package->PackageType->find('list',array('fields'=>array('packageTypeId','name')));

        $this->set(compact('packageAttributes'));
    }

    function validatePackage($data)
    {
        $fields = array(
            'siteId' => 'Site',
            'loaId' => 'Loa',
            'isBarter' => 'Barter/Remit',
            'packageStatusId' => 'Status',
            'packageName' => 'Working Name',
            'numGuests' => 'Max Num Guests',
            'minGuests' => 'Min Num Guests',
            'maxAdults' => 'Max Num Adults',
            'numNights' => 'Total Nights',
            'currencyId' => 'Currency',
            'disclaimer' => 'Disclaimer'
        );
        $errors = array();
        foreach ($fields as $fieldName => $displayName) {
            if ($fieldName == 'siteId') {
                if (empty($data['Package']['siteId'])) {
                    $errors[] = 'Site is missing.';
                } elseif ($data['Package']['siteId'] == 2) {
                    $ageRangeFields = array('rangeLow' => 'Minimum Age', 'rangeHigh' => 'Maximum Age');
                    foreach ($ageRangeFields as $rangeField => $label) {
                        if (empty($data['PackageAgeRange'][$rangeField]) && $data['PackageAgeRange'][$rangeField] != '0') {
                            $errors[] = "{$label} is missing.";
                        }
                    }
                }
            } elseif ($fieldName == 'disclaimer') {
                if (empty($data['Package']['customRateDisclaimerText'])) {
                    if (empty($data['Package']['rateDisclaimerDesc']) || empty($data['Package']['rateDisclaimerDate'])) {
                        $errors[] = 'Disclaimer is missing or incomplete.';
                    }
                }
            } elseif ($fieldName == 'numNights') {
                if (intval($data['Package']['numNights']) == 0) {
                    $errors[] = 'Total/Default Nights must be greater than 0.';
                } elseif ($data['Package']['isFlexPackage'] == 1) {
                    if ($data['Package']['numNights'] < $data['Package']['flexNumNightsMin'] || $data['Package']['numNights'] > $data['Package']['flexNumNightsMax']) {
                        $errors[] = 'Total/Default Nights must be within the Min/Max range for the Flex Package.';
                    }
                }
            } else {
                if (empty($data['Package'][$fieldName]) && $data['Package'][$fieldName] != '0') {
                    $errors[] = "{$displayName} is missing.";
                }
            }
        }
        return $errors;
    }

    function edit_room_loa_items($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $packageCurrencyId = $package['Package']['currencyId'];
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        if (!empty($this->data)) {
            //debug($this->data);
            //die();
            $items = 0;
            $packageLoaItemIds = array();
            $groupItems = $this->LoaItem->LoaItemGroup->getLoaItemIds($packageId);
            $isNewItem = false;
            if (isset($this->data['NewLoaItem'])) {
                $isNewItem = true;
                foreach ($this->data['NewLoaItem'] as $newRoom) {
                    //create new loa item. disabled for multi-client packages.
                    $data = array(
                        'loaItemTypeId' => 1,
                        'loaId' => $package['Loa']['loaId'],
                        'itemName' => $newRoom['itemName'],
                        'currencyId' => $packageCurrencyId
                    );
                    $this->LoaItem->create();
                    $this->LoaItem->save($data);
                    $loaItemId = $this->LoaItem->getLastInsertID();
                    //get existing rooms for the package (if they exist) for cloning rate periods, rates, and dates.
                    $roomLoaItems = $this->LoaItem->getRoomTypesByPackage($packageId);
                    if (empty($roomLoaItems)) {
                        //if there are no rooms associated to this package, check for existing loa items that the user checked off
                        //that we can use to clone. If there are none, the user will be prompted to create rate periods on next page
                        if (!empty($this->data['LoaItem'])) {
                            foreach ($this->data['LoaItem'] as $itemId => $item) {
                                if (isset($item['checked'])) {
                                    $this->LoaItem->recursive = -1;
                                    $roomLoaItems = $this->LoaItem->find(
                                        'all',
                                        array('conditions' => array('LoaItem.loaItemId' => $itemId))
                                    );
                                    break;
                                }
                            }
                        }
                    }
                    //clone 'em and create package rels
                    if (!empty($roomLoaItems)) {
                        if ($ratePeriods = $this->LoaItem->LoaItemRatePeriod->getRatePeriods(
                            $roomLoaItems[0]['LoaItem']['loaItemId'],
                            $packageId
                        )
                        ) {
                            foreach ($ratePeriods as $ratePeriod) {
                                $rp = array('LoaItemRatePeriod' => array('loaItemId' => $loaItemId));
                                $this->LoaItem->LoaItemRatePeriod->create();
                                $this->LoaItem->LoaItemRatePeriod->save($rp);
                                $ratePeriodId = $this->LoaItem->LoaItemRatePeriod->getLastInsertId();
                                if (isset($ratePeriod['LoaItemRate'])) {
                                    foreach ($ratePeriod['LoaItemRate'] as $rate) {
                                        $numNights = $rate['LoaItemRatePackageRel']['numNights'];
                                        $newRate = array('LoaItemRate' => array('loaItemRatePeriodId' => $ratePeriodId));
                                        for ($i = 0; $i <= 6; $i++) {
                                            $newRate['LoaItemRate']['w' . $i] = $rate['LoaItemRate']['w' . $i];
                                        }
                                        $this->LoaItem->LoaItemRatePeriod->LoaItemRate->create();
                                        $this->LoaItem->LoaItemRatePeriod->LoaItemRate->save($newRate);
                                        $rateId = $this->LoaItem->LoaItemRatePeriod->LoaItemRate->getLastInsertId();
                                        $rateRel = array(
                                            'LoaItemRatePackageRel' => array(
                                                'loaItemRateId' => $rateId,
                                                'packageId' => $packageId,
                                                'numNights' => $numNights
                                            )
                                        );
                                        $this->Package->LoaItemRatePackageRel->create();
                                        $this->Package->LoaItemRatePackageRel->save($rateRel);
                                    }
                                } else {
                                    $newRate = array('LoaItemRate' => array('loaItemRatePeriodId' => $ratePeriodId));
                                    for ($i = 0; $i <= 6; $i++) {
                                        $newRate['LoaItemRate']['w' . $i] = 1;
                                    }
                                    $this->LoaItem->LoaItemRatePeriod->LoaItemRate->create();
                                    $this->LoaItem->LoaItemRatePeriod->LoaItemRate->save($newRate);
                                    $rateId = $this->LoaItem->LoaItemRatePeriod->LoaItemRate->getLastInsertId();
                                    $rateRel = array(
                                        'LoaItemRatePackageRel' => array(
                                            'loaItemRateId' => $rateId,
                                            'packageId' => $packageId,
                                            'numNights' => $this->Package->field(
                                                'numNights',
                                                array('packageId' => $packageId)
                                            )
                                        )
                                    );
                                    $this->Package->LoaItemRatePackageRel->create();
                                    $this->Package->LoaItemRatePackageRel->save($rateRel);
                                }
                                if (!empty($ratePeriod['Validity'])) {
                                    foreach ($ratePeriod['Validity'] as $date) {
                                        unset($date['LoaItemDate']['loaItemDateId']);
                                        $date['LoaItemDate']['loaItemRatePeriodId'] = $ratePeriodId;
                                        $this->LoaItem->LoaItemRatePeriod->LoaItemDate->create();
                                        $this->LoaItem->LoaItemRatePeriod->LoaItemDate->save($date['LoaItemDate']);
                                    }
                                }
                            }
                        }
                    }

                    $rel = array(
                        'packageId' => $packageId,
                        'loaItemId' => $loaItemId,
                        'quantity' => $newRoom['quantity']
                    );
                    $this->Package->PackageLoaItemRel->create();
                    $this->Package->PackageLoaItemRel->save($rel);
                    $packageLoaItemIds[] = array(
                        'loaItemId' => $loaItemId,
                        'isNew' => 1,
                        'isExistingAddition' => 0,
                        'quantity' => $newRoom['quantity']
                    );
                    $items += $newRoom['quantity'];
                }
            }
            //now loop through existing items posted in the form
            foreach ($this->data['LoaItem'] as $loaItemId => $room) {
                if ($isMultiClientPackage) {
                    if ($itemExists = $this->LoaItem->getMultiClientRoomId($loaItemId, $packageId)) {
                        $loaItemId = $itemExists;
                    } else {
                        if (isset($room['checked'])) {
                            if (!$loaItemId = $this->LoaItem->createMultiClientRoom($loaItemId, $packageId)) {
                                $this->Session->setFlash('An error occurred saving this package.');
                                $this->redirect(
                                    '/clients/' . $clientId . '/packages/edit_room_loa_items/' . $packageId
                                );
                                return;
                            }
                        }
                    }
                }
                if (isset($room['checked'])) {
                    //handle cases where the user changed the quantity a room from more than 1 to only 1
                    if ($room['quantity'] == 1 && count(array_keys($groupItems, $loaItemId)) > 1) {
                        $this->deleteGroup($packageId, $loaItemId);
                        foreach (array_keys($groupItems, $loaItemId) as $groupItem) {
                            unset($groupItems[$groupItem]);
                        }
                    }
                    $isNewItem = false;
                    //select a room to validate rate periods/validity against
                    if (isset($room['PackageLoaItemRel']) && !$isMultiClientPackage) {
                        $masterLoaItemId = $loaItemId;
                        $isMaster = 1;
                    } else {
                        $isMaster = 0;
                    }
                    $items += $room['quantity'];
                    $packageLoaItemIds[] = array(
                        'loaItemId' => $loaItemId,
                        'isNew' => 0,
                        'isMaster' => $isMaster,
                        'quantity' => $room['quantity']
                    );
                } else {
                    //if this room had previously been related to this package, remove rels
                    if (isset($room['PackageLoaItemRel'])) {
                        //if this package has a group of rooms, delete the group
                        if (!empty($groupItems)) {
                            if (in_array($loaItemId, $groupItems)) {
                                if (count($groupItems) >= 2) {
                                    $this->deleteGroup($packageId, $loaItemId);
                                }
                            }
                        }
                        $this->Package->PackageLoaItemRel->delete($room['PackageLoaItemRel']['packageLoaItemRelId']);
                        $this->Package->LoaItemRatePackageRel->deleteRatesFromPackage($packageId, $loaItemId);
                    }
                }
            }
            //if this package has more than one room, create loaItemGroup
            if (count($packageLoaItemIds) > 1 || (count($packageLoaItemIds) == 1 && $items > 1)) {
                //if this is multiple quantities of the same room
                if (count($packageLoaItemIds) == 1 && $items > 1) {
                    for ($i = 1; $i < $items; $i++) {
                        $packageLoaItemIds[$i] = $packageLoaItemIds[0];
                    }
                } //skip validation if this is a multi-client package. package-specific rate periods are created in the next step
                elseif (count($packageLoaItemIds) > 1 && !$isMultiClientPackage) {
                    if (!isset($masterLoaItemId)) {
                        $masterLoaItemId = $packageLoaItemIds[0]['loaItemId'];
                    }
                    //validate that the rooms can be grouped together
                    $masterValidities = $this->LoaItem->LoaItemRatePeriod->LoaItemDate->getValidDates($masterLoaItemId);
                    foreach ($packageLoaItemIds as $loaItem) {
                        if ($loaItem['loaItemId'] == $masterLoaItemId) {
                            $isValidLoaItem = true;
                            continue;
                        } elseif ($loaItem['isNew']) {
                            $isValidLoaItem = true;
                            break;
                        }
                        //validate that rooms have same start and end dates. return with error message if not
                        if (!$loaItem['isNew'] && !$loaItem['isMaster']) {
                            $isValidLoaItem = true;
                            $thisValidities = $this->LoaItem->LoaItemRatePeriod->LoaItemDate->getValidDates(
                                $loaItem['loaItemId']
                            );
                            if (count($masterValidities) != count($thisValidities)) {
                                $isValidLoaItem = false;
                            } else {
                                foreach ($thisValidities as $validity) {
                                    if ($key = $this->array_search_key(
                                        $validity['startDate'],
                                        $masterValidities,
                                        'startDate'
                                    )
                                    ) {
                                        $isValidLoaItem = true;
                                        if ($validity['endDate'] != $masterValidities[$key]['endDate']) {
                                            $isValidLoaItem = false;
                                        }
                                    }
                                }
                            }
                            if (!$isValidLoaItem) {
                                $this->Session->setFlash(
                                    'These room nights are not eligible to be included in the same package. They must have the same number of rate periods and their date ranges must match completely'
                                );
                                $this->redirect(
                                    '/clients/' . $clientId . '/packages/edit_room_loa_items/' . $packageId
                                );
                                return;
                            }
                        }
                    }
                }
                //using the primary client loaId to create the room group item
                $loaItemId = $this->LoaItem->createRoomGroup($packageLoaItemIds, $package['Loa']['loaId'], $packageId);
                $loaItemGroupId = $loaItemId;
                $packageLoaItemIds[] = array('loaItemId' => $loaItemId, 'quantity' => 1, 'isGroupItem' => true);
            }
            //finally, update room loa items and update rels
            foreach ($packageLoaItemIds as $packageLoaItem) {
                $loaItemId = $packageLoaItem['loaItemId'];
                $query = "SELECT * FROM loaItem LoaItem
                        WHERE LoaItem.loaItemId = {$packageLoaItem['loaItemId']}";
                $loaItem = $this->Package->query($query);
                //debug($loaItem);
                //die();
                if ($packageLoaItemRel = $this->Package->PackageLoaItemRel->find(
                    'first',
                    array(
                        'conditions' => array(
                            'PackageLoaItemRel.packageId' => $packageId,
                            'PackageLoaItemRel.loaItemId' => $loaItemId
                        )
                    )
                )
                ) {
                    if ($packageLoaItem['quantity'] != $packageLoaItemRel['PackageLoaItemRel']['quantity']) {
                        $packageLoaItemRel['PackageLoaItemRel']['quantity'] = $packageLoaItem['quantity'];
                        $this->Package->PackageLoaItemRel->create();
                        $this->Package->PackageLoaItemRel->save($packageLoaItemRel);
                    }
                } else {
                    $rel = array(
                        'packageId' => $packageId,
                        'loaItemId' => $loaItemId,
                        'quantity' => $packageLoaItem['quantity']
                    );
                    $this->Package->PackageLoaItemRel->create();
                    $this->Package->PackageLoaItemRel->save($rel);
                }
                if ($ratePeriods = $this->LoaItem->LoaItemRatePeriod->getRatePeriods($loaItemId, $packageId)) {
                    foreach ($ratePeriods as $ratePeriod) {
                        if (isset($ratePeriod['LoaItemRate']) && !empty($ratePeriod['LoaItemRate'])) {
                            foreach ($ratePeriod['LoaItemRate'] as $rate) {
                                if (!$loaItemRatePackageRel = $this->Package->LoaItemRatePackageRel->find(
                                    'all',
                                    array(
                                        'conditions' => array(
                                            'LoaItemRatePackageRel.packageId' => $packageId,
                                            'LoaItemRatePackageRel.loaItemRateId' => $rate['LoaItemRate']['loaItemRateId']
                                        )
                                    )
                                )
                                ) {
                                    $rel = array(
                                        'packageId' => $packageId,
                                        'loaItemRateId' => $rate['LoaItemRate']['loaItemRateId'],
                                        'numNights' => $this->Package->field(
                                            'numNights',
                                            array('Package.packageId' => $packageId)
                                        )
                                    );
                                    $this->Package->LoaItemRatePackageRel->create();
                                    $this->Package->LoaItemRatePackageRel->save($rel);
                                }
                            }
                        } else {
                            $newRate = array('loaItemRatePeriodId' => $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']);
                            $this->LoaItem->LoaItemRatePeriod->LoaItemRate->create();
                            $this->LoaItem->LoaItemRatePeriod->LoaItemRate->save($newRate);
                            $rateId = $this->LoaItem->LoaItemRatePeriod->LoaItemRate->getLastInsertID();
                            $rel = array(
                                'packageId' => $packageId,
                                'loaItemRateId' => $rateId,
                                'numNights' => $this->Package->field(
                                    'numNights',
                                    array('Package.packageId' => $packageId)
                                )
                            );
                            $this->Package->LoaItemRatePackageRel->create();
                            $this->Package->LoaItemRatePackageRel->save($rel);
                        }
                    }
                }
            }
            $param = ($isNewItem) ? '?isNewItem=true' : '';
            $this->redirect('/clients/' . $clientId . '/packages/edit_room_nights/' . $packageId . $param);
        } else {
            foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
                $roomLoaItems = $this->LoaItem->getRoomTypesByLoa(
                    $packageClient['ClientLoaPackageRel']['loaId'],
                    $packageCurrencyId,
                    $packageId,
                    $isMultiClientPackage
                );
                $packageClient['ClientLoaPackageRel']['Rooms'] = $roomLoaItems;
            }
            //debug($package);
            //die();

            //acarney 2010-11-08 -- moving this logic to sql statement
            // PKGR - currency
            //foreach ($roomLoaItems as $k => $item) {
            //  if ($item['LoaItem']['currencyId'] !== $packageCurrencyId) {
            //      unset($roomLoaItems[$k]);
            //  }
            //}

            $this->set('package', $package);
        }
    }

    /*
     * @documentation: by BTurner
     * @why: Becu
     */
    function edit_inclusions($clientId, $packageId)
    {
        $loaId = $this->Package->ClientLoaPackageRel->getLoaId($packageId);
        $package = $this->Package->getPackage($packageId);
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        if (!empty($this->data)) {
            $this->autoRender = false;
            foreach ($this->data as $loaItem) {
                if (!empty($loaItem['AddInclusion'])) {
                    foreach ($loaItem['AddInclusion'] as $loaItemId => $inclusion) {
                        if (isset($inclusion['perNight'])) {
                            if ($isMultiClientPackage) {
                                $clientLoaId = $this->Package->PackageLoaItemRel->LoaItem->field(
                                    'loaId',
                                    array('LoaItem.loaItemId' => $loaItemId)
                                );
                                $clientRoomId = $this->Package->PackageLoaItemRel->LoaItem->getClientRoomId(
                                    $clientLoaId,
                                    $packageId
                                );
                                $quantity = $this->Package->LoaItemRatePackageRel->getNumNights(
                                    $clientRoomId,
                                    $packageId
                                );
                            } else {
                                $quantity = $package['Package']['numNights'];
                            }
                        } else {
                            $quantity = 1;
                        }
                        $newInclusion = array(
                            'PackageLoaItemRel' => array(
                                'loaItemId' => $loaItemId,
                                'packageId' => $packageId,
                                'quantity' => $quantity
                            )
                        );
                        $this->Package->PackageLoaItemRel->create();
                        $this->Package->PackageLoaItemRel->save($newInclusion);
                    }
                    continue;
                }
                if (!empty($loaItem['LoaItem'])) {
                    if ($errors = $this->validateInclusions($loaItem['LoaItem'])) {
                        echo json_encode($errors);
                        return;
                    }
                    foreach ($loaItem['LoaItem'] as $item) {
                        $data['LoaItem'] = $item;
                        $data['LoaItem']['loaId'] = $loaId;
                        $data['LoaItem']['currencyId'] = $this->Package->Currency->getPackageCurrencyId($packageId);
                        $this->LoaItem->create();
                        if ($this->LoaItem->save($data)) {
                            if (empty($loaItem['loaItemId'])) {
                                $loaItemId = $this->LoaItem->getLastInsertId();
                            } else {
                                $loaItemId = $item['loaItemId'];
                            }
                            $item['PackageLoaItemRel']['loaItemId'] = $loaItemId;
                            if (!empty($item['PackageLoaItemRel'])) {
                                if (empty($item['PackageLoaItemRel']['perNight'])) {
                                    $item['PackageLoaItemRel']['quantity'] = 1;
                                } else {
                                    $item['PackageLoaItemRel']['quantity'] = $item['PackageLoaItemRel']['clientNumNights'];
                                }
                                unset($item['PackageLoaItemRel']['perNight']);
                            } else {
                                $item['PackageLoaItemRel']['quantity'] = 1;
                            }
                            $item['PackageLoaItemRel']['packageId'] = $packageId;
                            $this->Package->PackageLoaItemRel->create();
                            $this->Package->PackageLoaItemRel->save($item);
                        }
                    }
                } else {
                    if (!empty($loaItem['PackageLoaItemRel'])) {
                        if (empty($loaItem['PackageLoaItemRel']['perNight'])) {
                            $loaItem['PackageLoaItemRel']['quantity'] = 1;
                        } else {
                            $loaItem['PackageLoaItemRel']['quantity'] = $loaItem['PackageLoaItemRel']['clientNumNights'];
                        }
                        unset($loaItem['PackageLoaItemRel']['perNight']);
                    } else {
                        $loaItem['PackageLoaItemRel']['quantity'] = 1;
                    }
                    $loaItem['PackageLoaItemRel']['packageId'] = $packageId;
                    $this->Package->PackageLoaItemRel->create();
                    $this->Package->PackageLoaItemRel->save($loaItem);
                }
            }
            echo "ok";
        }
        $this->set('isMultiClientPackage', $isMultiClientPackage);
        $numNights = $this->Package->field('numNights', array('Package.packageId' => $packageId));
        foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
            $packageClient['ExistingInclusions'] = $this->LoaItem->getPackageInclusions(
                $packageId,
                $packageClient['ClientLoaPackageRel']['loaId']
            );
            $packageClient['AvailableLoaItems'] = $this->LoaItem->getAvailableInclusions(
                $packageClient['ClientLoaPackageRel']['loaId'],
                $packageId,
                $package['Package']['currencyId']
            );
            if ($roomNights = $this->LoaItem->getRoomTypesByPackage($packageId)) {
                $roomLabel = array();
                foreach ($roomNights as $room) {
                    if ($room['LoaItem']['loaId'] == $packageClient['ClientLoaPackageRel']['loaId']) {
                        if ($isMultiClientPackage) {
                            if ($roomNumNights = $this->Package->LoaItemRatePackageRel->getNumNights(
                                $room['LoaItem']['loaItemId'],
                                $packageId
                            )
                            ) {
                                $roomLabel[] = $roomNumNights . ' nights in a ' . $room['LoaItem']['itemName'];
                            } else {
                                $roomLabel[] = $room['LoaItem']['itemName'];
                            }
                        } else {
                            $roomLabel[] = $room['LoaItem']['itemName'];
                        }
                    }
                }
                if ($isMultiClientPackage) {
                    $packageClient['roomLabel'] = implode('<br />', $roomLabel);
                    $packageClient['numNights'] = $roomNumNights;
                } else {
                    $packageClient['roomLabel'] = $numNights . ' nights in ' . implode(' and ', $roomLabel);
                    $packageClient['numNights'] = $numNights;
                }
            }
        }
        $itemTypes = $this->LoaItem->LoaItemType->getItemTypes();
        $currencyCode = $this->Package->Currency->getPackageCurrencyCode($packageId);

        if ($package['Package']['isTaxIncluded'] == 1 && !empty($roomNights[0]['LoaItem']['loaItemId'])) {
            if ($taxes = $this->LoaItem->Fee->getFeesForRoomType($roomNights[0]['LoaItem']['loaItemId'])) {
                $taxArr = array();
                foreach ($taxes as $tax) {
                    $taxArr[] = $tax['Fee']['feeName'];
                }
                $this->set('taxLabel', implode(' and ', $taxArr));
            }
        }

        // PKGR - currency
        $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
        $this->set('currencyCodes', $currencyCodes);
        //$this->set('inclusions', $existingInclusions);
        $this->set('itemTypes', $itemTypes);
        $this->set('currencyCode', $currencyCode);
        $this->set('numNights', $numNights);
        $this->set('clientId', $clientId);
        $this->set('packageId', $packageId);
        $this->set('package', $package);
    }

    function render_available_inclusions($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $loaId = $this->Package->ClientLoaPackageRel->getLoaId($packageId);
        foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
            $packageClient['LoaItems'] = $this->LoaItem->getAvailableInclusions(
                $packageClient['ClientLoaPackageRel']['loaId'],
                $packageId,
                $package['Package']['currencyId']
            );
        }
        $itemTypes = $this->LoaItem->LoaItemType->getItemTypes();

        // PKGR - currency
        $currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
        $this->set('currencyCodes', $currencyCodes);
        $this->set('package', $package);
        $this->set('itemTypes', $itemTypes);
    }

    function validateInclusions($data)
    {
        $errors = array();
        foreach ($data as $loaItem) {
            if (empty($loaItem['itemName'])) {
                $errors[] = 'Inclusion name is missing.';
            }
            if (empty($loaItem['loaItemTypeId'])) {
                $errors[] = 'Inclusion type is missing.';
            }
            if (empty($loaItem['itemBasePrice']) && $loaItem['itemBasePrice'] != '0') {
                $errors[] = 'Price is missing';
            }
        }
        return $errors;
    }

    function delete_inclusion_from_package($clientId, $packageId)
    {
        $this->autoRender = false;
        if ($this->Package->PackageLoaItemRel->delete($_GET['packageLoaItemRelId'])) {
            echo "ok";
        } else {
            echo json_encode($this->Package->validationErrors);
        }
        return;
    }

    function render_create_inclusion_form($clientId, $packageId)
    {
        $loaItemTypes = $this->LoaItem->LoaItemType->getItemTypes();
        $this->Package->recursive = -1;
        $numNights = $this->Package->field('numNights', array('Package.PackageId' => $packageId));
        $this->set('numNights', $numNights);
        $this->set('loaItemTypes', $loaItemTypes);
        $this->set('currencyCode', $this->Package->Currency->getPackageCurrencyCode($packageId));
        $this->set('i', $_GET['i']);
        $this->set('j', $_GET['j']);
    }

    function array_search_key($needle, $haystack, $field)
    {
        foreach ($haystack as $i => $item) {
            if (is_array($item)) {
                if (isset($item[$field])) {
                    if ($item[$field] == $needle) {
                        return $i;
                    }
                } else {
                    return array_search_key($needle, $item, $field);
                }
            }
        }
    }

    function deleteGroup($packageId, $loaItemId)
    {
        if ($groups = $this->LoaItem->LoaItemGroup->getGroup($packageId, $loaItemId)) {
            foreach ($groups as $group) {
                if (!empty($group['PackageLoaItemRel'])) {
                    $this->Package->PackageLoaItemRel->deletePackageLoaItemRel(
                        $group['PackageLoaItemRel']['packageLoaItemRelId']
                    );
                }
                $this->Package->LoaItemRatePackageRel->deleteRatesFromPackage(
                    $packageId,
                    $group['LoaItemGroup']['loaItemId']
                );
                $this->LoaItem->LoaItemGroup->deleteLoaItemGroup($group['LoaItemGroup']['loaItemGroupId']);
            }
            $groupId = $groups[0]['LoaItemGroup']['loaItemId'];
            $this->Package->PackageLoaItemRel->recursive = -1;
            if ($packageLoaItemRel = $this->Package->PackageLoaItemRel->find(
                'first',
                array('conditions' => array('PackageLoaItemRel.loaItemId' => $groupId))
            )
            ) {
                $this->Package->PackageLoaItemRel->deletePackageLoaItemRel(
                    $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId']
                );
            }
            $this->Package->LoaItemRatePackageRel->deleteRatesFromPackage($packageId, $groupId);
            $this->LoaItem->deleteLoaItem($groups[0]['LoaItemGroup']['loaItemId']);
        }
    }

    function edit_blackout_dates($clientId, $packageId)
    {

        if (!empty($this->data)) {

            if (LOGIT) {
                $this->Package->logit("\n\nstart edit_blackout_dates---------------------------\n\n");
            }

            $this->Package->recursive = -1;
            $arr = $this->Package->findByPackageId($packageId);
            $siteId = $arr['Package']['siteId'];
            $this->data['siteId'] = $siteId;
            $this->autoRender = false;
            foreach ($this->data['PackageBlackout'] as $key => $arr) {
                if (trim($arr['startDate']) == '') {
                    unset($this->data['PackageBlackout'][$key]);
                }
            }
            $this->Package->saveBlackouts($packageId, $this->data);
            $this->Package->logit($this->data);

            $this->Package->PricePoint->contain(array());
            $ppRows = $this->Package->PricePoint->findAllByPackageId($packageId);

            $issetArr = array();
            $loaItemRatePeriodIds = '';
            foreach ($ppRows as $ppRow) {
                // Weird cake bug where it sometimes returns 'PricePoint' and other times 'pricePoint'
                if (isset($ppRow['PricePoint'])) {
                    $ppArr = $ppRow['PricePoint'];
                } else {
                    if (isset($ppRow['pricePoint'])) {
                        $ppArr = $ppRow['pricePoint'];
                    } else {
                        $err = 'PricePoint key nor pricePoint key not found in array from findAllByPackageId for ';
                        $err .= 'packageId ' . $packageId;
                        mail(self::DEV_EMAIL, 'error in edit_blackout_dates()', $err);
                        continue;
                    }
                }
                $ppId = $ppArr['pricePointId'];
                $old_vgId = $ppArr['validityGroupId'];
                if (isset($issetArr[$ppId])) {
                    continue;
                }
                $issetArr[$ppId] = 1;

                $arr = $this->Package->PricePoint->getPricePointValidities($packageId, $ppId);
                if (empty($arr)) {
                    continue;
                }

                foreach ($arr as $key => $rows) {
                    $id = $rows['PricePointRatePeriodRel']['loaItemRatePeriodId'];
                    $pricePointLoaItemRatePeriodIdArr[$ppId][$id] = $id;
                }
                AppModel::logit($pricePointLoaItemRatePeriodIdArr);
            }

            if ($this->Package->updatePackagePricePointValidity($packageId, $siteId) === false) {
                mail(self::DEV_EMAIL, '1X error in edit_blackout_dates() packageId $packageId', 'eom');
            }

            // If the date range being posted is outside the date range of the loaItemDate, it will
            // not be retrieved by this. 
            foreach ($pricePointLoaItemRatePeriodIdArr as $ppId => $loaItemRatePeriodIdArr) {
                $loaItemRatePeriodIds = implode(",", array_unique($loaItemRatePeriodIdArr));
                $rows_db = $this->Package->getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds);
                if (($vgId = $this->Package->validityGroupWrapper($rows_db, $siteId)) !== false) {
                    $this->Package->updatePricePointValidityGroupId($ppId, $vgId);
                    $this->Package->updateOfferWithValidityGroupId($ppId, $siteId, $vgId);
                }
            }
            if (LOGIT) {
                $this->Package->logit("end edit_blackout_dates ---------------------------\n\n");
            }

            echo("ok");
            exit;

        } else {
            $blackout_weekday = $this->Package->getBlackoutWeekday($packageId);
            $blackout = $this->Package->getBlackout($packageId);

            $this->set('blackout', $blackout);
            $this->set('blackout_weekday', $blackout_weekday);
            $this->set('blackout_count', count($blackout));
            $this->set('packageId', $packageId);
            $this->set('clientId', $clientId);
            $this->set('weekdays', array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));

            $pkgVbDates = $this->Package->getPkgVbDates($packageId);
            if (!empty($pkgVbDates)) {
                if (empty($pkgVbDates['BlackoutDays'])) {
                    $pkgVbDates['BlackoutDays'] = array();
                }
                $this->set('current_validity', $pkgVbDates['ValidRanges']);
                $this->set('current_blackout', $pkgVbDates['BlackoutDays']);
            }
        }
    }

    function edit_publishing($clientId, $packageId)
    {

        if (!empty($this->data)) {
            $this->autoRender = false;
            $this->Package->save($this->data['Package']);
            if (isset($this->data['Inclusions']['order']) && !empty($this->data['Inclusions']['order'])) {
                $inclusions = explode(',', $this->data['Inclusions']['order']);
                $inclusions = array_values($inclusions);
                $this->Package->query("UPDATE packageLoaItemRel SET weight = 0 WHERE packageId = $packageId");
                foreach ($inclusions as $k => $inc) {
                    if (!$inc) {
                        continue;
                    }
                    $inc_save = array('packageLoaItemRelId' => $inc, 'weight' => $k);
                    $this->Package->PackageLoaItemRel->save($inc_save);
                }
            }

            echo("ok");
            exit;

        } else {
            $package = $this->Package->getPackage($packageId);
            $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
            $this->set('isMultiClientPackage', $isMultiClientPackage);
            $this->set('packageId', $packageId);
            $this->set('clientId', $clientId);
            $this->set('package', $package);
            $inclusions = $this->Package->getInclusions($packageId);

            if ($package['Package']['isTaxIncluded']) {
                $taxes_fees = array();
                $roomNights = $this->LoaItem->getRoomNights($packageId);
                $rn = $roomNights[0];
                if (isset($rn['Fees'])) {
                    foreach ($rn['Fees'] as $fee) {
                        if (trim($fee['Fee']['feeName'])) {
                            $taxes_fees[] = $fee['Fee']['feeName'];
                        }
                    }
                }
                if (!empty($taxes_fees)) {
                    $inclusions[] = array(
                        'LoaItem' => array(
                            'merchandisingDescription' => $this->getTaxesText(
                                $taxes_fees
                            )
                        )
                    );
                }
            }

            // get roomGradeName for this package
            $this->Package->PackageLoaItemRel->recursive = 2;
            $loaItems = $this->Package->PackageLoaItemRel->find(
                'all',
                array(
                    'conditions' => array(
                        'Package.packageId' => $packageId,
                        'LoaItem.loaItemTypeId' => array(1, 12, 22)
                    )
                )
            );

            $roomGrades = array();
            foreach ($loaItems as $loaItem) {
                if ($loaItem['LoaItem']['RoomGrade']['roomGradeName'] != '') {
                    $roomGrades[] = $loaItem['LoaItem']['RoomGrade']['roomGradeName'];
                }
            }
            $roomGradeName = implode(', ', $roomGrades);
            $this->set('roomGrade', $roomGradeName);

            if (!$isMultiClientPackage) {
                $inc = $this->LoaItem->getPackageInclusions(
                    $packageId,
                    $package['ClientLoaPackageRel'][0]['ClientLoaPackageRel']['loaId']
                );


                $roomNightDescription = $loaItems[0]['LoaItem']['merchandisingDescription'];


                foreach ($inc as $i) {
                    if (isset($i['LoaItem']['PackagedItems'])) {
                        $group_items = array();
                        foreach ($i['LoaItem']['PackagedItems'] as $gitems) {
                            if (!in_array($gitems['LoaItem']['loaItemTypeId'], array(1, 12))) {
                                $group_items[] = $gitems['LoaItem']['merchandisingDescription'];
                            } else {
                                $roomNightDescription = $gitems['LoaItem']['merchandisingDescription'];
                            }
                        }
                        $inclusions[] = array(
                            'Group' => 1,
                            'LoaItem' => $group_items,
                            'PackageLoaItemRel' => array('packageLoaItemRelId' => $i['PackageLoaItemRel']['packageLoaItemRelId'])
                        );
                    }
                }

                $roomNightDescription = str_replace("\n", '', $roomNightDescription);
                $this->set('roomNightDescription', $roomNightDescription);
                $this->set('items', $inclusions);
                //$packageTitle = ($package['Package']['siteId'] == 1 && empty($package['Package']['packageTitle'])) ? $roomGradeName . '
                // Package for ' . $package['Package']['numGuests'] . ' Travelers' : $package['Package']['packageTitle'];
                $packageTitle = (empty($package['Package']['packageTitle'])) ? $roomGradeName . ' Package for ' . $package['Package']['numGuests'] . ' Travelers' : $package['Package']['packageTitle'];
                $this->set('packageTitle', $packageTitle);
            }

        }
        if (empty($this->data)) {
            $this->data = $this->Package->read(null, $packageId);
        }
        $isFamilyPackage = $this->Package->isFamilyPackage($this->data);
        $this->set('isFamilyPackage', $isFamilyPackage);

    }

    function getTaxesText($taxes)
    {
        $taxes_fees_text = '';
        $count = count($taxes);
        foreach ($taxes as $k => $f) {
            $taxes_fees_text .= $f;
            if (($k + 2) == $count) {
                $taxes_fees_text .= ' &amp; ';
            } elseif (($k + 1) < $count) {
                $taxes_fees_text .= ', ';
            }
        }
        return $taxes_fees_text;
    }

    function edit_room_nights($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $siteId = $package['Package']['siteId'];
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        if (!empty($this->data)) {
            $this->autoRender = false;
            //echo print_r($this->data);
            //die();
            if ($errors = $this->validateRoomNights(
                $this->data,
                $package['Package']['numNights'],
                $isMultiClientPackage
            )
            ) {
                echo json_encode($errors);
                return;
            } else {
                $saveModels = array('LoaItemRate', 'Fee', 'LoaItemDate');
                foreach ($this->data as $i => $ratePeriod) {
                    $loaItems[] = $ratePeriod['LoaItems'];
                    if (key($ratePeriod) == 'taxesIncluded') {
                        $taxes = $package;
                        $taxes['Package']['isTaxIncluded'] = $this->data['Package']['taxesIncluded'];
                        $this->Package->create();
                        $this->Package->save($taxes);
                    } else {
                        foreach ($saveModels as $model) {
                            if ($model == 'LoaItemRate') {
                                $rateIds = array();
                                $this->Package->bindModel(array('hasMany' => array('LoaItemRate')));
                                foreach ($ratePeriod['LoaItems'] as $itemId => &$item) {
                                    if ($ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'] == '0') {
                                        $loaItemId = $item['LoaItem']['loaItemId'];
                                        $ratePeriodId = $this->LoaItem->LoaItemRatePeriod->createFromPackage(
                                            $loaItemId
                                        );
                                    } else {
                                        $ratePeriodId = $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'];
                                    }
                                    $query = "SELECT LoaItemRate.loaItemRateId, LoaItemRatePackageRel.loaItemRatePackageRelId
                                            FROM loaItemRate LoaItemRate
                                            INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING (loaItemRateId)
                                            WHERE LoaItemRatePackageRel.packageId = {$packageId} AND LoaItemRate.loaItemRatePeriodId = {$ratePeriodId}";
                                    if ($rates = $this->Package->query($query)) {
                                        foreach ($rates as $rate) {
                                            $rateIds[$rate['LoaItemRate']['loaItemRateId']] = $rate['LoaItemRatePackageRel']['loaItemRatePackageRelId'];
                                        }
                                    }
                                    if (isset($item['LoaItemRate'])) {
                                        foreach ($item['LoaItemRate'] as $j => &$rate) {
                                            if (isset($rate['loaItemRateId'])) {
                                                if (in_array($rate['loaItemRateId'], array_keys($rateIds))) {
                                                    unset($rateIds[$rate['loaItemRateId']]);
                                                }
                                            }
                                            if (empty($rate['loaItemRatePeriodId'])) {
                                                $rate['loaItemRatePeriodId'] = $ratePeriodId;
                                                $loaItems[0][$itemId]['LoaItemRate'][$j]['loaItemRatePeriodId'] = $ratePeriodId;
                                            }
                                        }
                                        if (!$this->Package->LoaItemRate->updateFromPackage(
                                            $item['LoaItemRate'],
                                            $packageId,
                                            null
                                        )
                                        ) {
                                            echo json_encode($this->Package->validationErrors);
                                            return;
                                        }
                                    }
                                }
                                if (!empty($rateIds)) {
                                    foreach ($rateIds as $loaItemRateId => $loaItemRatePackageRelId) {
                                        $this->LoaItem->LoaItemRate->delete($loaItemRateId);
                                        $this->LoaItem->LoaItemRate->LoaItemRatePackageRel->delete(
                                            $loaItemRatePackageRelId
                                        );
                                    }
                                }
                            } else {
                                if (isset($ratePeriod[$model])) {
                                    $this->Package->bindModel(array('hasMany' => array($model)));
                                    $saved = $this->Package->$model->updateFromPackage(
                                        $ratePeriod[$model],
                                        $loaItems[0],
                                        $packageId
                                    );
                                    if (!$saved) {
                                        echo json_encode($this->Package->validationErrors);
                                        return;
                                    }
                                }
                            }
                        }
                    }
                }
                $this->Package->updatePackagePricePointValidity($packageId, $siteId);
                echo("ok");
                exit;

            }
        } else {
            $this->set('package', $package);
            $this->set('isMultiClientPackage', $isMultiClientPackage);
            if ($roomNights = $this->LoaItem->getRoomNights($packageId)) {
                if (count($roomNights[0]['LoaItems'][0]['LoaItemRate']) > 1) {
                    $this->set('isDailyRates', true);
                }
                if ($isMultiClientPackage) {
                    foreach ($roomNights as &$night) {
                        foreach ($night['LoaItems'] as &$item) {
                            $item['Client']['name'] = $this->LoaItem->getClientName(
                                $item['LoaItem']['loaItemId'],
                                $packageId
                            );
                        }
                    }
                }
            } else {
                $roomNights = array();
            }
            //debug($roomNights);
            //die();
            $this->set('ratePeriods', $roomNights);
        }
    }

    function validateRoomNights($data, $packageNumNights, $isMultiClientPackage)
    {
        $errors = array();
        $dateRanges = array();
        foreach ($data as $i => $night) {
            if (key($night) == 'taxesIncluded') {
                continue;
            }
            if (isset($night['isNewItem'])) {
                if (count($night['LoaItemDate']) == 0) {
                    $errors[] = 'Rate periods must have at least one date range.';
                } else {
                    foreach ($night['LoaItemDate'] as $date) {
                        if (empty($date['startDate']) || empty($date['endDate'])) {
                            $errors[] = 'Date ranges must have both a start date and an end date.';
                        } elseif (strtotime($date['startDate']) >= strtotime($date['endDate'])) {
                            $errors[] = 'Start date cannot be later than the corresponding end date.';
                        }
                        $dateRanges[strtotime($date['startDate'])] = strtotime($date['endDate']);
                    }
                }
            }
            if ($i == 0 || $isMultiClientPackage) {
                $numNights = 0;
                foreach ($night['LoaItems'] as $item) {
                    foreach ($item['LoaItemRate'] as $j => $rate) {
                        if (isset($rate['isNew']) && (empty($rate['price']) || $rate['price'] == '')) {
                            $errors[] = 'Each rate period must have a price.';
                        }
                        if ($item['LoaItem']['loaItemTypeId'] == 1 || $isMultiClientPackage) {
                            if (empty($rate['LoaItemRatePackageRel']['numNights']) && $rate['LoaItemRatePackageRel']['numNights'] != '0') {
                                $errors[] = 'Number of nights must be filled in for each rate period.';
                            }
                            $numNights += $rate['LoaItemRatePackageRel']['numNights'];
                        } elseif ($item['LoaItem']['loaItemTypeId'] == 12) {
                            $numNights = $packageNumNights;
                        }
                    }
                }
            }
            if (($i == 0 || $isMultiClientPackage) && $numNights != $packageNumNights) {
                $errors[] = 'The quantity of the room night item(s) associated to this package must match the Total Nights set for the package.';
            }
        }
        if (!empty($dateRanges)) {
            ksort($dateRanges);
            foreach ($dateRanges as $startDate => $endDate) {
                if (!empty($lastRange)) {
                    if ($startDate <= $dateRanges[$lastRange]) {
                        $errors[] = 'One or more of the date ranges you have selected overlap each other. Date ranges must be mutually exclusive.';
                        break;
                    }
                }
                $lastRange = $startDate;
            }
        }
        if (count($errors) > 0) {
            return $errors;
        }
    }

    function edit_low_price_guarantees($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? 1 : 0;
        $ratePeriods = false;
        if (!empty($this->data)) {
            $this->autoRender = false;
            foreach ($this->data['LoaItemRatePackageRel'] as $loaItemRatePackageRelId => $guaranteePercentRetail) {
                $loaItemRatePeriod = array(
                    'loaItemRatePackageRelId' => $loaItemRatePackageRelId,
                    'guaranteePercentRetail' => $guaranteePercentRetail
                );
                $this->Package->LoaItemRatePackageRel->save($loaItemRatePeriod);
            }
            echo("ok");
            exit;
        } else {
            if (($r = $this->getRatePeriodsInfo($packageId)) !== false) {
                $ratePeriods = $r;
            }
        }
        $this->set('ratePeriods', $ratePeriods);
        $this->set('isMultiClientPackage', $isMultiClientPackage);
    }

    function edit_price_points($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $siteId = $package['Package']['siteId'];
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;

        // saving data
        if (!empty($this->data)) {

            /*
			Array
			(
					[PricePoint] => Array
							(
									[packageId] => 265754
									[pricePointId] => 26009
									[name] => High - Dec 19, 2012 - 2013
									[maxNumSales] =>
									[retailValue] => 3074
									[percentRetailAuc] => 50
									[percentRetailBuyNow] => 58
									[flexRetailPricePerNight] => 756
									[pricePerExtraNight] => 438
									[validityDisclaimer] => <b>This package is valid for travel:</b><br><br>December 19, 2012 - 
										September 3, 2013<br>Reservations are subject to availability at time of booking. 
										May not be valid during holidays and special event periods.
							)

					[gpr-22835] => 40
					[loaItemRatePeriodIds] => Array
							(
									[0] => 22836
							)

					[gpr-22836] => 40
					[guaranteedPercent] => 40
					[Package] => Array
							(
									[isFlexPackage] => 1
									[flexNumNightsMin] => 4
									[flexNumNightsMax] => 14
									[flexNotes] =>
									[overrideValidityDisclaimer] => 0
							)

			)


			*/

            $this->autoRender = false;

            // validation
            $errors = array();
            if (!$this->data['PricePoint']['name']) {
                $errors[] = 'The Name field is required.';
            }
            if (empty($this->data['loaItemRatePeriodIds'])) {
                $errors[] = 'You must choose at least one rate period.';
            }

            if ($this->data['PricePoint']['retailValue'] <= 0) {
                $errors[] = 'Retail Value must be greater than 0.';
            }

            $isNew = true;
            if (isset($this->data['PricePoint']['pricePointId'])) {
                $isNew = false;
            }

            $hasAuctionPrice = true;
            $hasBuyNowPrice = true;
            if (empty($this->data['PricePoint']['percentRetailAuc'])) {
                $hasAuctionPrice = false;
            }
            if (empty($this->data['PricePoint']['percentRetailBuyNow'])) {
                $hasBuyNowPrice = false;
            }

            // ticket1628
            // mbyrnes
            if ($hasAuctionPrice == false && $hasBuyNowPrice == false) {
                $errors[] = "Either an auction price OR a buy now price must be entered";
            }
            // If either field is NULL then check to see if there are existing scheduling masters
            // for this price point and corresponding offer type ID.If there are, then prompt
            if ($isNew == false && ($hasAuctionPrice == false || $hasBuyNowPrice == false)) {

                $ppid = $this->data['PricePoint']['pricePointId'];
                if ($hasAuctionPrice == false && $this->Package->SchedulingMaster->hasRow($ppid, 1) == false) {
                    $msg = "You must specify a percent retail amount because ";
                    $msg .= "offers have been scheduled for that auction.";
                    $errors[] = $msg;
                }
                if ($hasBuyNowPrice == false && $this->Package->SchedulingMaster->hasRow($ppid, 4) == false) {
                    $msg = "You must specify a percent retail amount because ";
                    $msg .= "offers have been scheduled for that buy now.";
                    $errors[] = $msg;
                }
            }

            if ($package['Package']['isFlexPackage'] == 1) {
                if (empty($this->data['PricePoint']['flexRetailPricePerNight']) || $this->data['PricePoint']['flexRetailPricePerNight'] <= 0) {
                    $errors[] = 'Flex Per Night Retail must be greater than 0.';
                }
                if (empty($this->data['PricePoint']['pricePerExtraNight']) || $this->data['PricePoint']['pricePerExtraNight'] <= 0) {
                    $errors[] = 'Flex Per Night Price must be greater than 0.';
                }
            }
            if (!$isMultiClientPackage) {
                if ((!$this->data['PricePoint']['percentRetailAuc'] && !$this->data['PricePoint']['percentRetailBuyNow']) || (!$this->data['auctionOverride'] && $this->data['PricePoint']['percentRetailAuc'] && $this->data['PricePoint']['percentRetailAuc'] < $this->data['guaranteedPercent']) || (!$this->data['buynowOverride'] && $this->data['PricePoint']['percentRetailBuyNow'] && $this->data['PricePoint']['percentRetailBuyNow'] < $this->data['guaranteedPercent'])) {

                    $errors[] = 'Percent of retail must be greater than or equal to the guaranteed percent of retail.';
                }
            }

            if (!empty($errors)) {
                echo json_encode($errors);
                return;
            }

            $vd = $this->data['PricePoint']['validityDisclaimer'];
            $this->data['PricePoint']['validityDisclaimer'] = str_replace(array('\n', '\r', "\n", "\r"), '', $vd);
            $this->data['PricePoint']['validityDisclaimer'] = Sanitize::escape($vd);

            $maxGuaranteedPercent = 0;
            foreach ($this->data['loaItemRatePeriodIds'] as $loaItemRatePeriodId) {
                //gpr = Guaranteed Percent of Retail
                $checkPercent = $this->data['gpr-' . $loaItemRatePeriodId];
                if ($checkPercent > $maxGuaranteedPercent) {
                    $maxGuaranteedPercent = $checkPercent;
                }
            }
            $this->data['PricePoint']['percentReservePrice'] = $maxGuaranteedPercent;

            // edit pricePoint
            $pricePointId = $this->data['PricePoint']['pricePointId'];
            if ($pricePointId) {
                // pricePoint
                /*
				Array
				(
						[packageId] => 264478
						[pricePointId] => 22696
						[name] => MID: Apr-May 2012
						[maxNumSales] => 
						[retailValue] => 1318
						[percentRetailAuc] => 70
						[percentRetailBuyNow] => 70
						[flexRetailPricePerNight] => 489.3235
						[pricePerExtraNight] => 0
						[validityDisclaimer] => <!--startheader--><b>This package is valid for Sunday through Tuesday 
							arrivals:</b><!--endheader--><br><br>April 3, 2012 - May 31, 2012<br>Reservations are subject 
							to availability at time of booking.<br><br><b>Blackout dates:</b><br><br>May 27-28, 2012<br>
							May not be valid during other holidays and special event periods.
						[percentReservePrice] => 0
				)
				*/
                $this->Package->PricePoint->save($this->data['PricePoint']);

                // remove pricePointRatePeriodRels and add below
                $this->Package->PricePoint->PricePointRatePeriodRel->deleteAll(
                    array(
                        'PricePointRatePeriodRel.pricePointId' => $pricePointId
                    ),
                    false
                );

                // add pricePoint
            } else {
                $this->Package->PricePoint->create();
                $this->Package->PricePoint->save($this->data['PricePoint']);
                $pricePointId = $this->Package->PricePoint->id;
            }

            // add pricePointRatePeriodRel
            foreach ($this->data['loaItemRatePeriodIds'] as $loaItemRatePeriodId) {
                $pricePointRatePeriodRel = array(
                    'pricePointId' => $pricePointId,
                    'loaItemRatePeriodId' => $loaItemRatePeriodId
                );
                $this->Package->PricePoint->PricePointRatePeriodRel->create();
                $this->Package->PricePoint->PricePointRatePeriodRel->save($pricePointRatePeriodRel);
            }

            // if override for package validity disclaimer, save this shiz
            $overrideVD = 0;
            if (isset($this->data['Package']['overrideValidityDisclaimer'])) {
                $overrideVD = $this->data['Package']['overrideValidityDisclaimer'];
            }
            if ($overrideVD == 1) {
                $package = array();
                $package['packageId'] = $packageId;
                $package['overrideValidityDisclaimer'] = 1;
                $this->Package->save($package);
            }

            $this->data['Package']['packageId'] = $packageId;

            //TICKET4367: Toolbox - When a Flex Package is switched to regular package, update minNights and maxNights to null
            if ($this->data['Package']['isFlexPackage'] !== '1'){
                $this->data['Package']['flexNumNightsMin'] = null;
                $this->data['Package']['flexNumNightsMax'] = null;
                $this->data['Package']['flexNotes'] = null;
                $this->Package->save($this->data['Package']);
            }else{
                $this->Package->save($this->data['Package']);
            }


            // ticket1870 - we still need to set validityStart and validityEnd in the pricePoint table
            $this->Package->updatePackagePricePointValidity($packageId, $siteId);
            // validityGroup stuff 
            $ppid = isset($this->data['PricePoint']['pricePointId']) ? trim(
                $this->data['PricePoint']['pricePointId']
            ) : '';
            if ($ppid != '') {
                $ppid = $this->data['PricePoint']['pricePointId'];
            } else {
                $ppid = $this->Package->PricePoint->id;
            }
            $loaItemRatePeriodIds = implode(",", $this->data['loaItemRatePeriodIds']);
            $rows_db = $this->Package->getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds, '', '');

            $vg_id = $this->Package->validityGroupWrapper($rows_db, $siteId);
            $this->Package->updatePricePointValidityGroupId($ppid, $vg_id);
            $this->Package->updateOfferWithValidityGroupId($ppid, $siteId, $vg_id, false);

            echo("ok");
            exit;

            // view data
        } else {

            // edit state
            $loaItemRatePeriodIds = array();
            $pricePointId = isset($this->params['url']['pricePointId']) ? $this->params['url']['pricePointId'] : 0;
            if ($pricePointId) {
                $pricePoint = $this->Package->PricePoint->find(
                    'first',
                    array(
                        'conditions' => array('PricePoint.pricePointId' => $pricePointId),
                        'contain' => array('PricePointRatePeriodRel')
                    )
                );
                $this->set('pricePoint', $pricePoint['PricePoint']);

                $pricePointRatePeriodRels = $this->Package->PricePoint->PricePointRatePeriodRel->find(
                    'all',
                    array(
                        'conditions' => array('PricePointRatePeriodRel.pricePointId' => $pricePointId),
                        'fields' => array('PricePointRatePeriodRel.loaItemRatePeriodId')
                    )
                );
                foreach ($pricePointRatePeriodRels as $pricePointRatePeriodRel) {
                    $loaItemRatePeriodIds[] = $pricePointRatePeriodRel['PricePointRatePeriodRel']['loaItemRatePeriodId'];
                }

                // Why is this being updated when data is only being viewed?
                // - Problem of packages not setting pricePoint validityStart or validityEnd, but gets it correctly set
                // if resaved. Apparently this is a work around to that, so putting it back in.
                //$this->Package->updatePackagePricePointValidity($packageId, $siteId);

            }

            $this->set('loaItemRatePeriodIds', $loaItemRatePeriodIds);

            $this->set('isMultiClientPackage', $isMultiClientPackage);
            if (isset($package['Package']['overrideValidityDisclaimer']) && $package['Package']['overrideValidityDisclaimer'] == 1) {
                $this->set('overrideValidityDisclaimer', 1);
            } else {
                $this->set('overrideValidityDisclaimer', 0);
            }

            $vd = '';
            if (isset($pricePoint['PricePoint']['validityDisclaimer'])) {
                $vd = $pricePoint['PricePoint']['validityDisclaimer'];
            }
            $this->set('vd', $vd);

            $this->set('isEdit', $pricePointId);

            $ratePeriods = $this->getRatePeriodsInfo($packageId);
            $ratePeriods = is_array($ratePeriods) ? $ratePeriods : array();

            // disable ratePeriods that have already been used
            $pricePointRatePeriods = $this->Package->PricePoint->getLoaItemRatePeriod($packageId);

            foreach ($ratePeriods as $key => $ratePeriod) {
                foreach ($pricePointRatePeriods as $pricePointRatePeriod) {
                    if ($ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'] == $pricePointRatePeriod['PricePointRatePeriodRel']['loaItemRatePeriodId'] && !in_array(
                            $pricePointRatePeriod['PricePointRatePeriodRel']['loaItemRatePeriodId'],
                            $loaItemRatePeriodIds
                        )
                    ) {
                        $ratePeriods[$key]['used'] = true;
                    }
                }
            }

            foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
                $loaId = $packageClient['ClientLoaPackageRel']['loaId'];
                $packageClient['ExistingInclusions'] = $this->LoaItem->getPackageInclusions($packageId, $loaId);
                if ($roomNights = $this->LoaItem->getRoomTypesByPackage($packageId)) {
                    $roomLabel = array();
                    foreach ($roomNights as $room) {
                        if ($room['LoaItem']['loaId'] == $packageClient['ClientLoaPackageRel']['loaId']) {
                            if ($isMultiClientPackage) {
                                $loaItemId = $room['LoaItem']['loaItemId'];
                                if ($roomNumNights = $this->Package->LoaItemRatePackageRel->getNumNights(
                                    $loaItemId,
                                    $packageId
                                )
                                ) {
                                    $roomLabel[] = $roomNumNights . ' nights in a ' . $room['LoaItem']['itemName'];
                                } else {
                                    $roomLabel[] = $room['LoaItem']['itemName'];
                                }
                            } else {
                                $roomLabel[] = $room['LoaItem']['itemName'];
                            }
                        }
                    }
                    if ($isMultiClientPackage) {
                        $packageClient['roomLabel'] = implode('<br />', $roomLabel);
                    } else {
                        $numNights = $package['Package']['numNights'];
                        $packageClient['roomLabel'] = $numNights . ' nights in ' . implode(' and ', $roomLabel);
                    }
                }
            }

            if ($package['Package']['isTaxIncluded'] == 1 && !empty($roomNights[0]['LoaItem']['loaItemId'])) {
                if ($taxes = $this->LoaItem->Fee->getFeesForRoomType($roomNights[0]['LoaItem']['loaItemId'])) {
                    $taxArr = array();
                    foreach ($taxes as $tax) {
                        $taxArr[] = $tax['Fee']['feeName'];
                    }
                    $this->set('taxLabel', implode(' and ', $taxArr));
                }
            }

            $this->set('ratePeriods', $ratePeriods);
            $this->set('clientId', $clientId);
            $this->set('packageId', $packageId);
            $this->set('package', $package);

        }
        //end else for view data

    }

    function ajaxGetPricePointValidityDisclaimer($clientId, $packageId)
    {

        $this->autoRender = false;
        $this->layout = false;
        $loaItemRatePeriodIds = $this->params['url']['ids'];
        //$loaItemRatePeriodIds = $_GET['ids'];
        if (!$loaItemRatePeriodIds) {
            die();
        }
        $ids = explode(',', $loaItemRatePeriodIds);
        foreach ($ids as $k => $id) {
            if (empty($id)) {
                unset($ids[$k]);
            }
        }
        $ids_string = implode(',', $ids);
        //$dates = $this->Package->getPricePointDateRange($packageId, $ids_string);
        //if (!$dates) {
        //	die();
        //}
        echo $this->Package->getValidityDisclaimerText(
            $packageId,
            $dates['minStartDate'],
            $dates['maxEndDate'],
            $ids_string
        );
    }

    //acarney 2010-11-08 -- may be deprecated... does not appear to be called anywhere
    function getRoomTypes($packageId)
    {
        $this->autoRender = false;
        $roomLoaItems = $this->LoaItem->getRoomTypesByLoa($packageId);
        $list = $this->prepList($roomLoaItems, 'LoaItem', 'itemName');
        echo print_r($list, true);
    }

    function prepList($data, $model, $labelName = 'name')
    {
        $list = array();
        if (!empty($data)) {
            $idVar = strtolower($model{0}) . substr($model, 1) . 'Id';
            foreach ($data as $d) {
                $item = $d[$model][$labelName] . '|' . $d[$model][$idVar];
                array_push($list, $item);
            }
        }
        return implode("\n", $list);
    }

    function clone_package($clientId, $packageId)
    {
        $this->layout = false;
        $this->autoRender = false;
        $newPackageId = $this->Package->clonePackage($packageId);

        if ($newPackageId && is_numeric($newPackageId)) {
            $this->redirect('/clients/' . $clientId . '/packages/summary/' . $newPackageId);
        } else {
            die('ERROR(S) HAVE BEEN DETECTED.<br /><br />' . $newPackageId);
        }
    }

    function excel($clientId, $packageId)
    {
        // Setup PackageExcel
        $this->_generateExportData($clientId, $packageId);
        if (!isset($_GET['debug'])) {
            // Output Spreadsheet
            Configure::write('debug', 0);
            App::import(
                'Vendor',
                'PackageExcel',
                array('file' => 'PackageExcel' . DS . 'PackageExcel.php')
            );
            $pe = new PackageExcel($this->viewVars);
            $pe->modifySheet();
            $pe->dump('Package-' . $packageId);
            die();
        }
    }

    function export($clientId, $packageId)
    {
        $this->layout = false;
        $this->Client->recursive = -1;
        $this->_generateExportData($clientId, $packageId);
        $package = $this->viewVars['package'];
        if ($package['Package']['siteId'] == 1) {
            $this->render('export');
        } else {
            $this->render('export_family');
        }
    }

    private final function _generateExportData($clientId, $packageId)
    {
        $package = $this->Package->getPackage($packageId);
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        $client = $this->Client->read(null, $clientId);
        $roomNights = $this->LoaItem->getRoomNights($packageId);
        $days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        foreach ($roomNights as $r => $rn) {
            foreach ($rn['Validity'] as $v => $vd) {
                $roomNights[$r]['Validity'][$v]['LoaItemDate']['startDate'] = date(
                    'M d Y',
                    strtotime($vd['LoaItemDate']['startDate'])
                );
                $roomNights[$r]['Validity'][$v]['LoaItemDate']['endDate'] = date(
                    'M d Y',
                    strtotime($vd['LoaItemDate']['endDate'])
                );
            }
            if (count($rn['LoaItems'][0]['LoaItemRate'] > 1)) {
                foreach ($rn['LoaItems'][0]['LoaItemRate'] as $bb => $rate) {
                    $days_arr = array();
                    for ($i = 0; $i < 7; $i++) {
                        if ($rate['LoaItemRate']["w$i"] == 1) {
                            $days_arr[] = $days[$i];
                        }
                    }
                    $roomNights[$r]['LoaItems'][0]['LoaItemRate'][$bb]['LoaItemRate']['MultiDayPrice'] = implode(
                        ', ',
                        $days_arr
                    );
                }
            }
        }

        // blackout validity
        $pkgVbDates = $this->Package->getPkgVbDates($packageId);
        if (!empty($pkgVbDates)) {
            if (empty($pkgVbDates['BlackoutDays'])) {
                $pkgVbDates['BlackoutDays'] = array();
            }
            $this->set('validity', $pkgVbDates['ValidRanges']);
            $this->set('blackout', $pkgVbDates['BlackoutDays']);
        }

        // blackout weekday
        $bo_weekdays = $this->Package->getBlackoutWeekday($packageId);
        $bo_weekdays_arr = array();
        foreach (explode(',', $bo_weekdays) as $w) {
            $bo_weekdays_arr[] = $this->Package->pluralize($w);
        }
        $this->set('bo_weekdays', implode(' ', $bo_weekdays_arr));

        //$loaItems = $this->Package->getLoaItems($packageId);
        foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
            $packageClient['Inclusions'] = $this->LoaItem->getPackageInclusions(
                $packageId,
                $packageClient['ClientLoaPackageRel']['loaId']
            );
        }
        $lowPriceGuarantees = $this->getRatePeriodsInfo($packageId);
        $this->set('ratePeriods', $roomNights);
        $this->set('package', $package);
        $this->set('isMultiClientPackage', $isMultiClientPackage);
        $this->set('client', $client['Client']); // sigh
        $this->set('roomNights', $roomNights);
        $this->set('vb', $this->Package->getPkgVbDates($packageId));
        $this->set('lowPrice', $lowPriceGuarantees);
        $this->set('cc', $package['Currency']['currencyCode']);
    }

    function render_rate_period($clientId, $packageId)
    {
        $package = $this->Package->find('first', array('conditions' => array('Package.packageId' => $packageId)));
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;
        $loaItems['LoaItems'] = $this->Package->PackageLoaItemRel->LoaItem->getRoomTypesByPackage($packageId);
        $rates = $this->LoaItem->getRoomNights($packageId);
        if (count($rates[0]['LoaItems'][0]['LoaItemRate']) > 1) {
            $this->set('isDailyRates', true);
        }
        foreach ($loaItems['LoaItems'] as $i => &$item) {
            $item['LoaItemRatePeriod']['loaItemRatePeriodId'] = 0;
            if (empty($rates)) {
                $item['LoaItemRate'][0]['LoaItemRate'][0] = array('loaItemRateId' => '', 'price' => '');
                for ($j = 0; $j <= 6; $j++) {
                    $item['LoaItemRate'][0]['LoaItemRate'][0]['w' . $j] = 0;
                }
            } else {
                foreach ($rates[0]['LoaItems'][0]['LoaItemRate'] as $k => $rate) {
                    $item['LoaItemRate'][$k]['LoaItemRate'] = array('loaItemRateId' => '', 'price' => '');
                    for ($j = 0; $j <= 6; $j++) {
                        $item['LoaItemRate'][$k]['LoaItemRate']['w' . $j] = $rate['LoaItemRate']['w' . $j];
                    }
                }
            }
            if ($isMultiClientPackage) {
                $item['Client']['name'] = $this->LoaItem->getClientName($item['LoaItem']['loaItemId'], $packageId);
            }
        }
        $this->Package->PackageLoaItemRel->LoaItem->Fee->recursive = -1;
        $loaItems['Fees'] = $this->Package->PackageLoaItemRel->LoaItem->Fee->find(
            'all',
            array('conditions' => array('Fee.loaItemId' => $loaItems['LoaItems'][0]['LoaItem']['loaItemId']))
        );
        $loaItems['Totals']['totalAccommodations'] = '0';
        $loaItems['Validity'] = array();

        $this->set('ratePeriods', $rates);
        $this->set('loaItems', $loaItems);
        $this->set('package', $package);
        $this->set('i', $_GET['i']);
    }

    function render_datepicker($clientId, $packageId)
    {
        $range = array(
            'LoaItemDate' => array(
                'loaItemRatePeriodId' => $_GET['ratePeriodId'],
                'loaItemDateId' => '',
                'startDate' => '',
                'endDate' => ''
            )
        );
        $this->set('range', $range);
        $this->set('index', $_GET['index']);
        $this->set('i', $_GET['i']);
    }

    function delete_date_range($clientId, $packageId)
    {
        $this->autoRender = false;
        foreach ($this->data as $ratePeriod) {
            if (empty($ratePeriod['LoaItemRatePeriod'])) {
                foreach ($this->data[0]['LoaItemDate'] as $date) {
                    if (empty($date['loaItemDateId'])) {
                        echo "ok";
                        return;
                    } else {
                        $this->Package->deleteDate($date['loaItemDateId']);
                        echo "ok";
                    }
                }
            } else {
                $delModels = array('LoaItemRate', 'LoaItemDate', 'LoaItemRatePeriod');
                foreach ($delModels as $model) {
                    if (isset($ratePeriod[$model])) {
                        $this->Package->bindModel(array('hasMany' => array($model)));
                        $this->Package->$model->deleteFromPackage($ratePeriod[$model]);
                    }
                }
            }

        }
    }

    function getRatePeriodsInfo($packageId)
    {
        // package
        $package = $this->Package->getPackage($packageId);
        $isMultiClientPackage = (count($package['ClientLoaPackageRel']) > 1) ? true : false;

        // currency
        $currency = $this->Package->getCurrency($packageId);

        $totals = array();
        foreach ($package['ClientLoaPackageRel'] as $packageClient) {
            $loaItems = $this->Package->getLoaItems($packageId, $packageClient['ClientLoaPackageRel']['clientId']);
            // get total for inclusion
            $inclusionTotal = 0;
            $total = 0;
            foreach ($loaItems as $loaItem) {
                if (isset($loaItem['LoaItem']['itemBasePrice'])) {
                    $taxes = $this->Package->getTaxes($loaItem['PackageLoaItemRel']['loaItemId']);
                    if (!in_array($loaItem['LoaItem']['loaItemTypeId'], array(1, 12, 22))) {
                        $total = $loaItem['LoaItem']['itemBasePrice'] * $loaItem['PackageLoaItemRel']['quantity'];
                        $inclusionTotal += $total + ($total * $taxes['percent'] / 100) + $taxes['fixed'];
                        // add taxes
                    }
                }
            }
            $totals[$packageClient['Client']['name']] = array('inclusionTotal' => $inclusionTotal);
        }

        $ratePeriods = $this->Package->getRatePeriods($packageId);

        if (!empty($ratePeriods)) {
            foreach ($ratePeriods as $key => &$ratePeriod) {
                $clientName = $this->LoaItem->getClientName($ratePeriod['LoaItemRatePeriod']['loaItemId'], $packageId);

                // get dates
                $loaItemDates = $this->Package->getLoaItemDates(
                    $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']
                );
                $loaDates = array();
                foreach ($loaItemDates as $loaItemDate) {
                    $loaDates[] = date('M j, Y', strtotime($loaItemDate['LoaItemDate']['startDate'])) . ' - ' . date(
                            'M j, Y',
                            strtotime($loaItemDate['LoaItemDate']['endDate'])
                        );
                }

                $loaItemRatePeriodId = $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'];
                $pricePoint = $this->LoaItem->LoaItemRatePeriod->PricePointRatePeriodRel->getPricePointForRatePeriod(
                    $packageId,
                    $loaItemRatePeriodId
                );
                if ($pricePoint) {
                    $ratePeriod['PricePoint'] = $pricePoint['PricePoint'];
                    $ratePeriods[$key]['PricePoint'] = $pricePoint['PricePoint'];
                    $ratePeriod['PricePointRatePeriodRel'] = $pricePoint['PricePointRatePeriodRel'];
                    $ratePeriods[$key]['PricePointRatePeriodRel'] = $pricePoint['PricePointRatePeriodRel'];
                }

                // get room rate
                $total = $this->Package->getRoomRate(
                    $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'],
                    $packageId
                );
                $startPrice = ($total + $totals[$clientName]['inclusionTotal']) * $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail'] / 100;

                // assign new fields
                $ratePeriods[$key]['packageId'] = $packageId;
                $ratePeriods[$key]['currencyCode'] = $currency['Currency']['currencyCode'];
                $ratePeriods[$key]['weeklyExchangeRateToDollar'] = $currency['CurrencyExchangeRate']['weeklyExchangeRateToDollar'];
                $ratePeriods[$key]['dateRanges'] = implode('<br/>', $loaDates);
                $ratePeriods[$key]['startPrice'] = round($startPrice);
                $ratePeriods[$key]['retailValue'] = round($total + $totals[$clientName]['inclusionTotal']);
                $ratePeriods[$key]['auctionPrice'] = (empty($ratePeriod['PricePoint']['percentRetailAuc'])) ? round(
                    $total + $totals[$clientName]['inclusionTotal']
                ) : round(
                    ($total + $totals[$clientName]['inclusionTotal']) * ($ratePeriod['PricePoint']['percentRetailAuc'] / 100)
                );
                $ratePeriods[$key]['buyNowPrice'] = (empty($ratePeriod['PricePoint']['percentRetailBuyNow'])) ? round(
                    $total + $totals[$clientName]['inclusionTotal']
                ) : round(
                    ($total + $totals[$clientName]['inclusionTotal']) * ($ratePeriod['PricePoint']['percentRetailBuyNow'] / 100)
                );
                if (isset($ratePeriod['PricePoint'])) {
                    $ratePeriods[$key]['percentRetailAuc'] = $ratePeriod['PricePoint']['percentRetailAuc'];
                    $ratePeriods[$key]['percentRetailBuyNow'] = $ratePeriod['PricePoint']['percentRetailBuyNow'];
                    $ratePeriods[$key]['pricePointId'] = $ratePeriod['PricePoint']['pricePointId'];
                }
                $ratePeriods[$key]['usdStartPrice'] = ($currency['Currency']['currencyId'] == 1) ? '' : "$(" . round(
                        $startPrice * $currency['CurrencyExchangeRate']['weeklyExchangeRateToDollar']
                    ) . ")";
                $ratePeriods[$key]['usdRetailValue'] = ($currency['Currency']['currencyId'] == 1) ? '' : "($" . round(
                        $total * $currency['CurrencyExchangeRate']['weeklyExchangeRateToDollar']
                    ) . ")";
                $ratePeriods[$key]['clientName'] = $clientName;
                if (true || $package['Package']['isFlexPackage']) {
                    $ratePeriods[$key]['roomRetailPricePerNight'] = $total / $package['Package']['numNights'];
                    $ratePeriods[$key]['flexPricePerNight'] = (!empty($ratePeriod['PricePoint']['pricePerExtraNight'])) ? $ratePeriod['PricePoint']['pricePerExtraNight'] : $this->LoaItem->calcFlexPricePerNight(
                        $ratePeriod['LoaItemRatePeriod']['loaItemId'],
                        $packageId,
                        $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']
                    );
                }
            }
        }

        return $ratePeriods;
    }

    // for assigning all offers an id that correlates to a set of validity date ranges
    // these date ranges will then be searchable by users
    // see updateOfferWithGroupId()
    function migrateValidityDates()
    {

        xdebug_disable();
        //exit("set to fg or ll and uncomment insert into validityGroup table");
        $debug_q = true;
        // display queries that execute
        $debug_q = false;
        // display queries that execute

        $siteId = 2;
        //fg - change as needed
        $table = "offerFamily";
        //$siteId=1;//LL - change as needed
        //$table="offerLuxuryLink";

        $start_point = 0;
        $offset = 100;
        $num_rows = 1;
        while ($num_rows) {
            /*
			 $q="SELECT * FROM package p
			 INNER JOIN packageValidityDisclaimer pv USING (packageId)
			 WHERE p.modified > '2011-05-01' AND p.modified<'2011-05-25' AND pv.isBlackout = 1
			 GROUP BY pv.packageId";
			 */

            /*
			 $q="SELECT packageId FROM $table p ";
			 $q.="WHERE validityGroupId=0 AND startDate<NOW() AND endDate>NOW() AND ISCLOSED=0 ";
			 */
            $q = "SELECT packageId FROM $table p WHERE packageId IN (260660,261581,261745,261919,260727,260005,260006)";

            echo "<p>$q</p>";
            $rows = $this->Package->query($q);
            $pkid_arr = array();
            while (list($key, $arr) = each($rows)) {
                //$pkid_arr[]=$arr['offerLuxuryLink']['packageId'];
                $pkid_arr[] = $arr['p']['packageId'];
            }
            if (count($pkid_arr) == 0) {
                exit("no rows found in $table with validityGroupId as 0");
            }
            $pkid_arr = array_unique($pkid_arr);
            echo "<p>" . count($pkid_arr) . " rows found</p>";
            flush();

            $q = "SELECT packageId, pp.pricePointId, loaItemRatePeriodId FROM pricePoint pp ";
            $q .= "INNER JOIN pricePointRatePeriodRel USING (pricePointId) ";
            $q .= "INNER JOIN package USING (packageId) ";
            $q .= "WHERE package.siteId=$siteId ";
            if (count($pkid_arr) > 0) {
                $q .= "AND package.packageId IN (" . implode(", ", $pkid_arr) . ") ";
            }
            $q .= "ORDER BY packageId,pricePointId ASC ";
            $q .= "LIMIT $start_point, $offset";
            $res = $this->Package->query($q);
            $num_rows = count($res);
            echo "<br><b>num_rows: $num_rows</b><br>";
            if ($debug_q) {
                echo "<p>$q</p>";
                //echo "<br>num rows:".count($res)."<br>";
            }

            flush();
            $validity_arr = array();
            foreach ($res as $key => $arr) {
                $packageId = $arr['pp']['packageId'];
                $pricePointId = $arr['pp']['pricePointId'];
                $loa_id = $arr['pricePointRatePeriodRel']['loaItemRatePeriodId'];
                if (isset($validity_arr[$packageId][$pricePointId]['loa_ids'])) {
                    $validity_arr[$packageId][$pricePointId]['loa_ids'] .= "," . $loa_id;
                } else {
                    $validity_arr[$packageId][$pricePointId]['loa_ids'] = $loa_id;
                }
            }

            foreach ($validity_arr as $packageId => $arr) {

                $loa_ids = '';
                foreach ($arr as $pricePointId => $loa_arr) {
                    $loa_ids = $loa_arr['loa_ids'];

                    echo "<br>packageId: $packageId<br>";
                    echo "loa_ids: $loa_ids<br>";

                    $dates = $this->Package->getPackageValidityDisclaimerByItem($packageId, $loa_ids, 0, 0, $debug_q);
                    print_r($dates);
                    /*
					 $vg_id=$this->IdCreator->genId();
					 if ($vg_id==0){
					 echo "<p style='color:red;'>Failed to gen a vg_id for</p>";
					 echo "<pre>";
					 print_r($arr);
					 echo "</pre>";
					 exit;
					 }
					 */
                    // get existing validityGroupId for packageId
                    $q = "SELECT validityGroupId FROM $table WHERE packageId=$packageId AND pricePointId=$pricePointId";
                    $q .= " AND endDate>NOW()";
                    $q .= " GROUP BY validityGroupId";
                    $q_r = $this->Package->query($q);
                    echo "<p>$q</p>";
                    if (count($q_r) == 0) {
                        echo "<p style='color:red;'>Nothing found in $table for packageId: $packageId and ppid: $pricePointId</p>";
                        continue;
                    }
                    echo "<pre>";
                    print_r($q_r);
                    echo "</pre>";
                    $vg_id = $q_r[0][$table]['validityGroupId'];

                    echo $vg_id . "|";
                    if ($vg_id == 0) {
                        echo "<p style='color:red;'>0 vg_id pkid: $packageId ppid: $pricePointId</p>";
                        continue;
                    }
                    $argh = 0;
                    if (isset($dates['BlackoutDays']) && count($dates['BlackoutDays']) > 0) {
                        echo "<p>BlackoutDays</p>";
                        print "<pre>";
                        print_r($dates['BlackoutDays']);
                    } else {
                        echo "<p>No blackout days for vg_id: $vg_id pkid: $packageId ppid: $pricePointId</p>";
                        $argh++;
                    }
                    if (isset($dates['ValidRanges']) && count($dates['ValidRanges']) > 0) {
                        echo "ValidRanges";
                        print "<pre>";
                        print_r($dates['ValidRanges']);
                    } else {
                        echo "<p>No valid dates for vg_id: $vg_id pkid: $packageId ppid: $pricePointId</p>";
                        $argh++;

                    }
                    if ($argh == 2) {
                        echo "<p style='color:red;'>No valid ranges or black out days</p>";
                        continue;
                    }

                    $hasValidDate = false;
                    $doUpdate = false;

                    if (isset($dates['ValidRanges'])) {
                        foreach ($dates['ValidRanges'] as $arr) {
                            foreach ($arr as $key => $pvd_arr) {
                                if ($pvd_arr['endDate'] < date("Y-m-d")) {
                                    echo "<p>endDate:" . $pvd_arr['endDate'] . " is in the past. Skipping</p>";
                                    continue;
                                    //don't bother with validity end dates in the past
                                }
                                $hasValidDate = true;
                                $doUpdate = $this->Package->insertValidityGroup($vg_id, $pvd_arr, $siteId, $debug_q);
                            }
                        }
                    }

                    if (isset($dates['BlackoutDays'])) {
                        foreach ($dates['BlackoutDays'] as $arr) {
                            foreach ($arr as $key => $pvd_arr) {
                                if ($pvd_arr['endDate'] < date("Y-m-d")) {
                                    continue;
                                }
                                //don't bother with validity end dates in the past
                                $hasValidDate = true;
                                $q = "SELECT * FROM validityGroup WHERE validityGroupId=$vg_id ";
                                $q .= "AND startDate='" . $pvd_arr['startDate'] . "' AND endDate='" . $pvd_arr['endDate'] . "' ";
                                $q .= "AND isBlackout=1";
                                echo "<p>" . htmlspecialchars($q) . "</p>";
                                if (count($this->Package->query($q)) == 0) {
                                    echo "<p style='color:blue;'>Blackout date not found. Inserting</p>";
                                    $doUpdate = $this->Package->insertValidityGroup(
                                        $vg_id,
                                        $pvd_arr,
                                        $siteId,
                                        $debug_q
                                    );
                                } else {
                                    echo "<p style='color:green;'>Row already exists. Skipping insert.</p>";
                                }

                            }
                        }
                    }

                    if ($hasValidDate && $doUpdate) {
                        $this->Package->updatePricePointValidityGroupId($pricePointId, $vg_id, $debug_q);
                        if ($this->Package->updateOfferWithGroupId(
                                $pricePointId,
                                $vg_id,
                                $siteId,
                                $debug_q
                            ) === false
                        ) {

                        }
                    }

                }

                echo "<hr>";

            }

            $start_point += $offset;

        }

        $this->set("validity_arr", $validity_arr);

    }

}
