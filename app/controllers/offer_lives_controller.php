<?php
class OfferLivesController extends AppController {

	var $name = 'OfferLive';

	function index() {
		$this->OfferLive->recursive = 0;
		$this->set('offers', $this->paginate());
	}

	function edit($id = null) {
		if (!empty($this->data)) {
			if ($this->OfferLive->save($this->data)) {
				$this->Session->setFlash(__('The Offer has been modified', true), 'default', array(), 'success');
			} else {
				$this->Session->setFlash(__('The Offer could not be modified', true), 'default', array(), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->OfferLive->read(null, $id);
		}
	}
	
	//Limit access to this controller
	function isAuthorized() {
	    $authorized_groups = array('Merchandising', 'Geeks');
	    
	    if (in_array('Geeks', $this->user['LdapUser']['groups']) ||
	        in_array('Merchandising', $this->user['LdapUser']['groups'])) {
	        return true;
	    }
	    
	    return false;
	}
}
?>