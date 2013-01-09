<?php
class CreditBanksController extends AppController {

	var $name = 'CreditBanks';
	var $helpers = array('Time','Html');
	
	var $uses = array('CreditBank','CreditBankItem');
	
	function index() {
		
		$order = "creditBankId";
		
		$this->paginate = array(
			'joins' => array(
					array(
						'alias' => 'i',
						'table' => 'creditBankItem',
						'type' => 'LEFT',
						'conditions' => '`i.creditBankId` = `CreditBank.creditBankId`'
					)
				),
			'order' => $order,
			'limit' => 50,
			'fields' => array(
					'SUM(i.amountChange) as balance',
					'CreditBank.creditBankId',
					'CreditBank.dateCreated',
					'CreditBank.userId',
					'User.userId',
					'User.firstName',
					'User.lastName'
					),
			'group' => 'CreditBank.creditBankId',
		);
		
        $banks = $this->paginate();

		$this->set('banks', $banks);
	}
	
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Credit Bank', true));
			$this->redirect(array('action'=>'index'));
		}

		// if saving data
		if (!empty($this->data)) {	
			
			$this->CreditBank->alterCreditBank($this->data['CreditBank']['CreditBankId'], $this->data['CreditBankItem']['amountChange'], $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname']);
		}
		// if viewing data
		$this->data = $this->CreditBank->find('first',array(
			'joins' => array(
					array(
						'alias' => 'i',
						'table' => 'creditBankItem',
						'type' => 'LEFT',
						'conditions' => '`i.creditBankId` = `CreditBank.creditBankId`'
					)
				),
			'conditions' => array(
				'CreditBank.creditBankId' => $id
			),
			'fields' => array(
					'SUM(i.amountChange) as balance',
					'CreditBank.creditBankId',
					'CreditBank.dateCreated',
					'CreditBank.userId',
					'User.userId',
					'User.firstName',
					'User.lastName'
					),
			'group' => 'CreditBank.creditBankId'
		));

		if (!$this->data) {
			$this->Session->setFlash(__('Invalid Credit Bank #'.$id, true));
			$this->redirect(array('action'=>'index'));
		}

		// get list of donors for this ID
		$this->data['items'] = $this->CreditBankItem->find('all',array(
			'conditions' => array(
				'CreditBankItem.creditBankId' => $id,
				'CreditBankItem.isActive' => 1
			),
			'order' => 'CreditBankItem.dateCreated'
		));
		
		$this->set('creditBank', $this->data);
	}
	
}