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
		$noteId = $this->params['pass'][0];
		$noteType = $this->params['pass'][1];
		
		// get note data
		$result = $this->ClientNote->getNoteList($noteId, $noteType);
		
		// get note type name
		$noteTypeName = $this->ClientNote->getNoteType($noteType);
		
		$this->set('noteId', $noteId);
		$this->set('noteType', $noteType);
		$this->set('noteTypeName', $noteTypeName);
		$this->set('noteResults', $result);
		$this->set('noteUser', $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname']);
	}
	
	/***
	 * Saves a new client note to client specified by POST clientId
	 */
	function add(){
		
		$this->layout = 'ajax';
			
		// get values
		$author = $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname'];
		$message = $_POST['message'];
		$created = date('M, d Y @ g:i a');
		
		
		// if for client, save client info
		if(isset($_POST['noteId'])){		
			$noteId = $_POST['noteId'];	
			$noteType = $_POST['noteType'];
			
			// save new clientNote entry
			$noteId = $this->ClientNote->saveNote( $noteId, $author, $message, $noteType );
		}
		
		$this->set('author', $author);
		$this->set('message', $message);
		$this->set('created', $created);
		$this->set('noteId', $noteId);
	}
	
	/***
	 * Removes a new client note to client specified by POST clientId
	 */
	function remove(){
		
		$this->layout = 'ajax';
			
		// get values
		$author = $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname'];
		$noteId = $_POST['noteId'];
		
		// save new clientNote entry
		$this->ClientNote->removeNote( $noteId, $author );
		
	}
}


