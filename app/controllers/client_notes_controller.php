<?php
class ClientNotesController extends AppController {

	var $name = 'ClientNotes';
	var $helpers = array('Time','Html');
	
	function index() {

	}
	
	/***
	 * Grabs all client notes for client specified by GET input clientId and displays to screen
	 */
	function view(){
		
		// declare this function as an ajax call
		$this->layout = 'ajax';
		
		// get vars
		$clientId = $this->params['pass'][0];
		
		// get clientNote data
		$result = $this->ClientNote->getClientNoteList($clientId);
		
		$this->set('clientId', $clientId);
		$this->set('clientNoteResults', $result);
		$this->set('clientNoteUser', $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname']);
	}
	
	/***
	 * Saves a new client note to client specified by POST clientId
	 */
	function add(){
		
		$this->layout = 'ajax';
			
		// get values
		$author = $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname'];
		$message = $_POST['message'];
		$clientId = $_POST['clientId'];
		$created = date('M, d Y @ g:i a');
		
		// save new clientNote entry
		$clientNoteId = $this->ClientNote->saveClientNote( $clientId, $author, $message );
		
		$this->set('author', $author);
		$this->set('message', $message);
		$this->set('created', $created);
		$this->set('clientNoteId', $clientNoteId);
	}
	
	/***
	 * Removes a new client note to client specified by POST clientId
	 */
	function remove(){
		
		$this->layout = 'ajax';
			
		// get values
		$author = $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname'];
		$clientNoteId = $_POST['noteId'];
		
		// save new clientNote entry
		$this->ClientNote->removeClientNote( $clientNoteId, $author );
		
	}
}
?>



