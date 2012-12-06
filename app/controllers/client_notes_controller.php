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
	 * Grabs all user notes for a user specified by GET input userId and displays to screen
	 */
	function viewUserNotes(){
		
		// declare this function as an ajax call
		$this->layout = 'ajax';
		
		// get vars
		$userId = $this->params['pass'][0];
		
		// get clientNote data
		$result = $this->ClientNote->getUserNoteList($userId);
		
		$this->set('clientId', $userId);
		$this->set('clientNoteResults', $result);
		$this->set('clientNoteUser', $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname']);
	}
	
	/***
	 * Grabs all user notes for a user specified by GET input userId and displays to screen
	 */
	function viewPhotoNotes(){
		
		// declare this function as an ajax call
		$this->layout = 'ajax';
		
		// get vars
		$clientId = $this->params['pass'][0];
		
		// get clientNote data
		$result = $this->ClientNote->getPhotoNoteList($clientId);
		
		$this->set('clientId', $clientId);
		$this->set('clientNoteResults', $result);
		$this->set('clientNoteUser', $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname']);
	}
	
	/***
	 * Grabs all user notes for a user specified by GET input userId and displays to screen
	 */
	function viewTicketNotes(){
		
		// declare this function as an ajax call
		$this->layout = 'ajax';
		
		// get vars
		$ticketId = $this->params['pass'][0];
		
		// get clientNote data
		$result = $this->ClientNote->getTicketNoteList($ticketId);
		
		$this->set('clientId', $ticketId);
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
		$created = date('M, d Y @ g:i a');
		
		// if for client, save client info
		if(isset($_POST['clientId'])){		
			$clientId = $_POST['clientId'];
			$type = 1;
			
			// save new clientNote entry
			$clientNoteId = $this->ClientNote->saveClientNote( $clientId, $author, $message, $type );
		}
		else if(isset($_POST['userId'])){
			$clientId = $_POST['userId'];
			$type = 2;
			
			// save new clientNote entry
			$clientNoteId = $this->ClientNote->saveClientNote( $clientId, $author, $message, $type );
		}
		else if(isset($_POST['photoId'])){
			$clientId = $_POST['photoId'];
			$type = 3;
			
			// save new clientNote entry
			$clientNoteId = $this->ClientNote->saveClientNote( $clientId, $author, $message, $type );
		}
		else if(isset($_POST['ticketId'])){
			$clientId = $_POST['ticketId'];
			$type = 4;
			
			// save new clientNote entry
			$clientNoteId = $this->ClientNote->saveClientNote( $clientId, $author, $message, $type );
		}
		
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


