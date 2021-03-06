<?php
class LoaItemsController extends AppController {

	var $name = 'LoaItems';
	var $helpers = array('Html', 'Form', 'Ajax', 'Javascript');
	var $components = array('RequestHandler');
	var $uses = array('LoaItem', 'LoaItemRatePeriod', 'LoaItemRate', 'LoaItemDate', 'Loa', 'LoaItemGroup', 'Fee');
	var $isGroup = false;
    
    function beforeFilter() {
		parent::beforeFilter();
        $this->clientId = $this->LoaItem->Loa->getLoaClientId($this->params['pass'][0]);
        $this->set('clientId', $this->clientId);
		$this->set('currentTab', 'property');
	}

	function index() {
		$this->LoaItem->recursive = 0;
		$this->set('loaItems', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid LoaItem.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('loaItem', $this->LoaItem->read(null, $id));
		
		$loaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$this->set('loaItemTypeIds', ($loaItemTypeIds));
	}

	function edit_group($id = null) {
		$this->isGroup = true;
		$this->edit($id);
		$this->set('isGroup',1);
		$this->render('edit');
	}

	function add_group() {
		$this->isGroup = true;
		$this->add();
		$this->set('isGroup', 1);
		$this->render('add');
	}
   
	function add() {
		if (!empty($this->data)) {
				
			$this->LoaItem->create();
			
			$loaItemData = array();
			$loaItemData['LoaItem'] = $this->data['LoaItem'];
			$loaItemData['Fee'] = $this->data['Fee'];

			// get currency id from loa
			$loa = $this->LoaItem->query('SELECT currencyId FROM loa WHERE loaId = ' . $loaItemData['LoaItem']['loaId']);
			$currencyId = $loa[0]['loa']['currencyId'];
			$loaItemData['LoaItem']['currencyId'] = $currencyId;

			// handle group saves -- prepare LoaItemGroup 
			// ---------------------------------------------------------
			if ($this->isGroup) {
				foreach ($this->data['LoaItemGroup'] as $groupItemId => $quantity) {
					$loaItemData['LoaItemGroup'][] = array('groupItemId' => $groupItemId, 'quantity' => $quantity);
				}
			} 

			$fee_count = 0;
			foreach ($loaItemData['Fee'] as $k => $loa_item_fee) {
				$fee_count++;
				if (!trim($loa_item_fee['feeName']) && !trim($loa_item_fee['feePercent'])) {
					$fee_count--;
					unset($loaItemData['Fee'][$k]);	
				}
			}
		
			if ($fee_count < 1) {
				unset($loaItemData['Fee']);
			}

			// save the loaitem and related models -- then do the rate periods
			// ---------------------------------------------------------
			if ($this->LoaItem->saveAll($loaItemData)) {
				$this->Session->setFlash(__('The LoaItem has been saved', true));
				if (!empty($this->data['LoaItemRatePeriod'])) {
					foreach ($this->data['LoaItemRatePeriod'] as $k => $v) {
						$loaItemRatePeriod = array();
						$loaItemRatePeriod['LoaItemRatePeriod']['loaItemId'] = $this->LoaItem->id;
						$loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodName'] = $v['loaItemRatePeriodName'];
						$loaItemRatePeriod['LoaItemRate'] = array_values($v['LoaItemRate']);
						$loaItemRatePeriod['LoaItemDate'] = array_values($v['LoaItemDate']);
						foreach ($loaItemRatePeriod['LoaItemDate'] as $kd => $vd) {
							$loaItemRatePeriod['LoaItemDate'][$kd]['startDate'] = date('Y-m-d', strtotime($vd['startDate']));
							$loaItemRatePeriod['LoaItemDate'][$kd]['endDate'] = date('Y-m-d', strtotime($vd['endDate']));
						}
						$this->LoaItemRatePeriod->saveAll($loaItemRatePeriod);
					}
				}
				if ($this->data['LoaItem']['addAnother'] != 1):
				    if ($this->RequestHandler->isAjax()) {
					    $this->set('closeModalbox', true);
					    $this->Session->setFlash(__('The LoaItem has been saved', true), 'default', array(), 'success');
				    } else {
					    $this->redirect(array('controller' => 'loas', 'action'=>'view', 'id' => $this->params['data']['LoaItem']['loaId']));
				    }
				else:
				    $this->Session->setFlash(__('The LoaItem has been saved', true), 'default', array(), 'success');
				    unset($this->data['LoaItem']);
				endif;
			} else {
				$this->Session->setFlash(__('The LoaItem could not be saved. Please, try again.', true));
			}
		}
		
		// set loa item type ids -- different for groups
		// ---------------------------------------------------------
		$masterLoaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$loaItemTypeIds = $this->setLoaItemTypes($masterLoaItemTypeIds, $this->isGroup);
		$this->set('loaItemTypeIds', $loaItemTypeIds);
		$this->set('masterLoaItemTypeIds', $masterLoaItemTypeIds);
	
		// pull item rate period info for the views
		// ---------------------------------------------------------
		$this->Loa->recursive = 2;
		$loa = $this->Loa->read(null, $this->params['loaId']);
		$loa['LoaItem'] = $this->sortItems($loa['LoaItem']);
		foreach ($loa['LoaItem'] as $k => $item) {
			if (!empty($item['LoaItemRatePeriod'])) {
				foreach ($item['LoaItemRatePeriod'] as $a => $rp) {
					$tmp = $this->LoaItemRatePeriod->read(null, $rp['loaItemRatePeriodId']);
					$loa['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemRate'] = $tmp['LoaItemRate'];
					$loa['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemDate'] = $tmp['LoaItemDate'];
				}
			}
		}
		$this->set('loa', $loa);

		$roomGrade = array();
		foreach ($loa['Client']['RoomGrade'] as $rd) {
			$roomGrade[$rd['roomGradeId']] = $rd['roomGradeName'];
		}
		$this->set('roomGradeIds', $roomGrade);

		$this->set('day_map', array(0=>'Su', 1=>'M', 2=>'Tu', 3=>'W', 4=>'Th', 5=>'F', 6=>'Sa'));
		
		// currency stuff
		// ---------------------------------------------------------
		$this->set('currencyId', $loa['Loa']['currencyId']);
		$this->set('currencyCode', $loa['Currency']['currencyCode']);
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));

		// other vars
		// ---------------------------------------------------------
		$this->data['LoaItem']['loaId'] = $this->params['loaId'];
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid LoaItem', true));
			$this->redirect(array('action'=>'index'));
		}
		
		// set loa item type ids -- different for groups
		// ---------------------------------------------------------
		$masterLoaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$loaItemTypeIds = $this->setLoaItemTypes($masterLoaItemTypeIds, $this->isGroup);
		$this->set('loaItemTypeIds', $loaItemTypeIds);
		$this->set('masterLoaItemTypeIds', $masterLoaItemTypeIds);

		if (!empty($this->data)) {
			$loaItemData = array();
			$loaItemData['LoaItem'] = $this->data['LoaItem'];
			$loaItemData['Fee'] = $this->data['Fee'];
			
			// handle group saves -- remove fees and prepare LoaItemGroup 
			// ---------------------------------------------------------
			if ($this->isGroup) {
				foreach ($this->data['LoaItemGroup'] as $groupItemId => $quantity) {
					$loaItemData['LoaItemGroup'][] = array('groupItemId' => $groupItemId, 'quantity' => $quantity);
				}
				$loaItemGroupExisting = $this->LoaItemGroup->findAllByLoaItemId($loaItemData['LoaItem']['loaItemId']);
				$loaItemGroupExistingIds = array();
				foreach ($loaItemGroupExisting as $k => $v) {
					$loaItemGroupExistingIds[] = $v['LoaItemGroup']['loaItemGroupId'];
				}
			} 
			
			$fee_count = 0;
			foreach ($loaItemData['Fee'] as $k => $loa_item_fee) {
				$fee_count++;
				if (!trim($loa_item_fee['feeName']) && !trim($loa_item_fee['feePercent'])) {
					if (is_numeric($loa_item_fee['feeId'])) {
						$this->Fee->del($loa_item_fee['feeId']);
					}
					$fee_count--;
					unset($loaItemData['Fee'][$k]);	
				}
			}
		
			if ($fee_count < 1) {
				unset($loaItemData['Fee']);
			}
			
			// save the loaitem and related models -- then do the rate periods
			// ---------------------------------------------------------
			if ($this->LoaItem->saveAll($loaItemData)) {	

				if ($this->isGroup && !empty($loaItemGroupExistingIds)) {
					$this->LoaItemGroup->query("DELETE FROM loaItemGroup WHERE loaItemGroupId IN (". implode(',', $loaItemGroupExistingIds) .")");
				}
				
				if (!empty($this->data['LoaItemRatePeriod'])) {
					foreach ($this->data['LoaItemRatePeriod'] as $k => $v) {
						$loaItemRatePeriod = array();
						if ($v['loaItemRatePeriodId']) {
							$loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'] = $v['loaItemRatePeriodId'];
							$this->LoaItem->query("DELETE FROM loaItemRate WHERE loaItemRatePeriodId = $v[loaItemRatePeriodId]");
							$this->LoaItem->query("DELETE FROM loaItemDate WHERE loaItemRatePeriodId = $v[loaItemRatePeriodId]");
						}

						$loaItemRatePeriod['LoaItemRatePeriod']['loaItemId'] = $loaItemData['LoaItem']['loaItemId'];
						$loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodName'] = $v['loaItemRatePeriodName'];
						$loaItemRatePeriod['LoaItemRate'] = array_values($v['LoaItemRate']);
						$loaItemRatePeriod['LoaItemDate'] = array_values($v['LoaItemDate']);
						foreach ($loaItemRatePeriod['LoaItemDate'] as $kd => $vd) {
							$loaItemRatePeriod['LoaItemDate'][$kd]['startDate'] = date('Y-m-d', strtotime($vd['startDate']));
							$loaItemRatePeriod['LoaItemDate'][$kd]['endDate'] = date('Y-m-d', strtotime($vd['endDate']));
						}

						$this->LoaItemRatePeriod->saveAll($loaItemRatePeriod);

						// Ticket 2523 -- Flex rate doesn't recalc when changing tax rate
						// Need to re-calc FLEX price points when making fee changes
						$packageId = $this->LoaItem->getPackageIdByLoaId($loaItemData['LoaItem']['loaId']);
						$pricePoint = $this->LoaItemRatePeriod->PricePointRatePeriodRel->getPricePointForRatePeriod($packageId,$v['loaItemRatePeriodId']);
						$pricePoint = $pricePoint['PricePoint'];
						
						if ($pricePoint['flexRetailPricePerNight'] != NULL) {
							$pricePoint['flexRetailPricePerNight'] = $this->LoaItem->calcFlexPricePerNight($loaItemData['LoaItem']['loaItemId'], $packageId, $v['loaItemRatePeriodId']);
							$this->LoaItemRatePeriod->PricePointRatePeriodRel->PricePoint->save(array('PricePoint' => $pricePoint));
						
							$flexWarning = "<script>alert('WARNING: You have updated the pricing or taxes/fees for a Flex package. The Flex Retail Price Per Night has been updated, however, the price per EXTRA night has not.\\n\\nYou must manually edit the price point for this LOA and re-calculate the Flex price.\\n\\n');</script>";
						}
					}
				}

				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
					$this->Session->setFlash(__('The LoaItem has been saved'.$flexWarning, true), 'default', array(), 'success');
				} else {
					$this->redirect(array('controller' => 'loas', 'action'=>'view', 'id' => $this->params['data']['LoaItem']['loaId']));
				}
			} else {
				$this->Session->setFlash(__('The LoaItem could not be saved. Please, try again.', true));
			}
		}

		if (empty($this->data)) {
			$this->LoaItem->recursive = 2;
			$this->data = $this->LoaItem->read(null, $id);
			$groups = $this->data['LoaItemGroup'];
			$group_ids = array();
			foreach ($groups as $g) {
				$group_ids[$g['groupItemId']] = $g;
			}
		
			// pull item rate period info for the views
			// ---------------------------------------------------------
			$this->Loa->recursive = 2;
			$loa = $this->Loa->read(null, $this->data['Loa']['loaId']);
			$loa['LoaItem'] = $this->sortItems($loa['LoaItem']);
			foreach ($loa['LoaItem'] as $k => $item) {
				if (!empty($item['LoaItemRatePeriod'])) {
					foreach ($item['LoaItemRatePeriod'] as $a => $rp) {
						$tmp = $this->LoaItemRatePeriod->read(null, $rp['loaItemRatePeriodId']);
						$loa['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemRate'] = $tmp['LoaItemRate'];
						$loa['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemDate'] = $tmp['LoaItemDate'];
					}
				}
			}
			$this->set('loa', $loa);
			$roomGrade = array();
			foreach ($loa['Client']['RoomGrade'] as $rd) {
				$roomGrade[$rd['roomGradeId']] = $rd['roomGradeName'];
			}
			$this->set('roomGradeIds', $roomGrade);
			$this->set('day_map', array(0=>'Su', 1=>'M', 2=>'Tu', 3=>'W', 4=>'Th', 5=>'F', 6=>'Sa'));

			$this->set('groupIds', $group_ids);
			//$this->set('currencyId', $this->data['Loa']['currencyId']);
			//$this->set('currencyCode', $this->data['Loa']['Currency']['currencyCode']);
            
            $this->set('currencyId', $this->data['LoaItem']['currencyId']);
			// handle fees
			if (count($this->data['Fee']) < 3) {
				$feeTypeId2 = false;
				foreach ($this->data['Fee'] as $m => $n) {
					if ($n['feeTypeId'] == 2) {
						$feeTypeId2 = true;
					}
				}
				while (count($this->data['Fee']) < 3) {
					if (!$feeTypeId2) {
						$feeTypeId = 2;
						$feeTypeId2 = true;
					} else {
						$feeTypeId = 1;
					}
					$tmp = array(
						'feeId' => '',
						'feeTypeId' => $feeTypeId,
						'loaItemId' => $this->data['LoaItem']['loaItemId'],
						'feeName' => '',
						'feePercent' => '',
						'LoaItem' => $this->data['LoaItem']
					);
					$this->data['Fee'][] = $tmp;
				}
			}
			usort($this->data['Fee'], array('LoaItemsController','usortFeeTypeId'));
		}
        $this->LoaItem->bindModel(array('hasOne' => array('Currency' => array('foreignKey' => 'currencyId'))));
		$this->set('currencyCode', $this->LoaItem->Currency->getLoaItemCurrencyCode($id));
	}
    
    function clone_items($loaId) {
        $this->LoaItem->recursive = 1;
        if ($loaItems = $this->LoaItem->find('all', array('conditions' => array('LoaItem.loaId = '.$loaId,
                                                                                'LoaItem.loaItemTypeId NOT IN (12, 13, 14, 20, 21)')))) {
            $this->set('loaItems', $loaItems);
            $this->Loa->Currency->recursive = -1;
            $currencies = $this->Loa->Currency->find('list');
            $this->set('currencies', $currencies);
            $loas = $this->Loa->getClientLoas($this->clientId);
            $this->set('loas', $loas);
            $this->set('currentLoa', $loaId);
        }
        else {
            $this->Session->setFlash('No LOA Items available to clone.');
        }
        if (!empty($this->data)) {           
            if (isset($this->data['CloneItems'])) {
                foreach (array_keys($this->data['CloneItems']) as $itemId) {
                    $this->LoaItem->recursive = -1;
                    if ($loaItem = $this->LoaItem->find('first', array('conditions' => array('LoaItem.loaItemId' => $itemId),
                                                                       'fields' => array('LoaItem.loaItemTypeId',
                                                                                         'LoaItem.loaId',
                                                                                         'LoaItem.roomGradeId',
                                                                                         'LoaItem.itemName',
                                                                                         'LoaItem.itemBasePrice',
                                                                                         'LoaItem.perPerson',
                                                                                         'LoaItem.merchandisingDescription',
                                                                                         'LoaItem.currencyId')))) {
                        if ($loaItem['LoaItem']['loaId'] == $this->data['LoaItem']['loaId'] &&
                            $loaItem['LoaItem']['currencyId'] == $this->data['LoaItem']['currencyId']) {
                            $this->Session->setFlash('You cannot clone an LOA item with the same currency as the original for the same LOA. Please select a different currency or a different LOA');
                            return;
                        }
                        else {
                            $newItem = $loaItem;
                            $newItem['LoaItem']['createdFromItemId'] = $itemId;
                            $newItem['LoaItem']['loaId'] = $this->data['LoaItem']['loaId'];
                            $newItem['LoaItem']['currencyId'] = $this->data['LoaItem']['currencyId'];
                            if ($loaItem['LoaItem']['currencyId'] != $this->data['LoaItem']['currencyId']) {
                                $newItem['LoaItem']['itemBasePrice'] = 0.00;
                            }
                            $this->LoaItem->create();
                            $this->LoaItem->save($newItem);
                        }
                    }
                }
                $this->set('closeModalbox', true);
                $this->Session->setFlash(__('LOA items have been successfully cloned.', true), 'default', array(), 'success');
            }
            else {
                $this->Session->setFlash('You must select at least one LOA item to clone.');
            }
        }
    }

	function usortFeeTypeId($a, $b) {
		if ($a['feeTypeId'] == $b['feeTypeId']) {
			return 0;
		}
		return ($a['feeTypeId'] < $b['feeTypeId']) ? -1 : 1;
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for LoaItem', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->autoRender = false;
		$this->LoaItem->recursive = -1;
		$loaItem = $this->LoaItem->read(null, $id);
		if ($this->LoaItem->del($id)) {
			$this->Session->setFlash(__('LoaItem deleted', true));
			$this->redirect(array('controller' => 'loas', 'action'=>'items', $loaItem['LoaItem']['loaId']));
		}
	}
	
	function sortItems($data) {
		$loaItemTypeIds = array(19,1,6,7,5,8,15,16,3,17,18,11);
		$tmp = array();
		$ids = array();
		foreach ($loaItemTypeIds as $itemTypeId) {
			foreach ($data as $k=>$v) {
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
	
	function setLoaItemTypes($arr, $isGroup) {
		$new_arr = array();
		if ($isGroup) {
			$group_item_type_ids = array(12,13,14);
			foreach ($arr as $k => $v) {
				if (in_array($k, $group_item_type_ids)) {
					$new_arr[$k] = $v;
				}
			}
			return $new_arr;
		} else {
			$item_type_ids = array(19,1,6,7,5,8,15,16,3,17,18,11);
			foreach ($item_type_ids as $v) {
				if (isset($arr[$v])) {
					$new_arr[$v] = $arr[$v];
				}
			}
			return $new_arr;
		}
	}
}
?>
