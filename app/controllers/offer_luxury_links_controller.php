<?php
class OfferLuxuryLinksController extends AppController {

	var $name = 'OfferLuxuryLink';

	function index() {
		$this->OfferLuxuryLink->recursive = 0;
		$this->set('offers', $this->paginate());
	}

	function edit($id = null) {
		if (!empty($this->data)) {
			if ($this->OfferLuxuryLink->save($this->data)) {
				$this->Session->setFlash(__('The Offer has been modified', true), 'default', array(), 'success');
			} else {
				$this->Session->setFlash(__('The Offer could not be modified', true), 'default', array(), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->OfferLuxuryLink->read(null, $id);
		}
	}
	
	//Limit access to this controller
	function isAuthorized() {
	    $authorized_groups = array('Merchandising', 'Geeks');
	    
	    if (in_array('Geeks', $this->user['LdapUser']['groups']) ||
	        in_array('Merchandising', $this->user['LdapUser']['groups']) ||
	        in_array('publishing', $this->user['LdapUser']['groups'])
		) {
	        return true;
	    }
	    
	    return false;
	}
}
?>
