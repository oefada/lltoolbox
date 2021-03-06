<?php
class EventRegistriesController extends AppController {

	var $name = 'EventRegistries';
	var $helpers = array('Time','Html');
	var $uses = array('EventRegistry','EventRegistryDonor','Country');
	
	function index() {
		
		$order = "eventRegistryId";
		
		$this->paginate = array(
			'order' => $order,
			'limit' => 50,
			'joins' => array(
					array(
						'alias' => 'd',
						'table' => 'eventRegistryDonor',
						'type' => 'LEFT',
						'conditions' => '`d.eventRegistryId` = `EventRegistry.eventRegistryId`'
					)
				),
			'fields' => array(
					'SUM(d.amount) as balance',
					'EventRegistry.eventRegistryId',
					'EventRegistry.eventTitle',
					'EventRegistry.dateCreated',
					'EventRegistry.registryUrl',
					'EventRegistryType.eventName',
					'EventRegistry.eventRegistryId',
					'EventRegistry.registrant1_firstName',
					'EventRegistry.registrant1_lastName',
					'EventRegistry.eventRegistryId',
					'EventRegistry.userId',
					'User.userId',
					'User.firstName',
					'User.lastName'
					),
			'group' => 'EventRegistry.eventRegistryId'
		);
		
        $registries = $this->paginate();

		$this->set('registries', $registries);
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Client', true));
			$this->redirect(array('action'=>'index'));
		}

		// if saving data
		if (!empty($this->data)) {
			//var_dump($this->data);die;
			$this->EventRegistry->save($this->data);
		}
		// if viewing data
		if (empty($this->data)) {

			$this->data = $this->EventRegistry->find('first',array(
				'conditions' => array(
					'EventRegistry.eventRegistryId' => $id
				)
			));

			if (!$this->data) {
				$this->Session->setFlash(__('Invalid Event Registry #'.$id, true));
				$this->redirect(array('action'=>'index'));
			}
			
		}

		// get list of donors for this ID
		$this->data['donors'] = $this->EventRegistryDonor->find('all',array(
			'conditions' => array(
				'EventRegistryDonor.eventRegistryId' => $id
			)
		));
		
		// get list of countries
		$this->data['countries'] = $this->Country->find('list');
		
		$this->set('eventRegistry', $this->data);
	}
	
}