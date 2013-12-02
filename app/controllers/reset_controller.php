<?php
class ResetController extends AppController
{
	public $uses = array();

	private static $validFormats = array('json');
	private $returnData;

	/**
	 * Executed before every action in the controller
	 * We want to bypass auth for this method for now.
	 * 
	 * TODO: Secure this and perhaps require a security token
	 */
	 public function beforeFilter()
	 {
		 Configure::write('debug', 0);
		 $this->layout = 'ajax';
		 if (!ISSTAGE && !ISDEV) {
		 	echo "This script is only available on dev and stage.";
		 	die;
		 }
		 parent::beforeFilter();
		 $this->LdapAuth->allow('*');
	 }

	/**
	 * Remove all records associated with a given userId
	 * 
	 * @param	int userId of user records to delete
	 */
	public function user($userId = null, $commit = false)
	{
		$this->loadModel('User');

		// Check that $userId is set and is a number
		if (!is_null($userId) && is_numeric($userId)) {
			// cast the userId as an int since it comes in as a string
			$userId = (int) $userId;
			
			if ($this->User->userExists($userId)) {
				// Proceed to delete the user and related records
				if ($this->User->deleteUserById($userId) === TRUE) {
					$this->setReturnData("User ID $userId deleted successfully.", true);
				} else {
					$this->setReturnData("User ID $userId could not be deleted.", false);
				}
			} else {
				// The given userId doesn't exist
				$this->setReturnData("User ID $userId does not exist.", false);
			}
		} else {
			$this->setReturnData('Required parameter userId was not provided');
		}

		$this->set('returnData', $this->getReturnData());
	}

	/**
	 * 
	 */
	private function setReturnData($message, $status = false)
	{
		$this->returnData = array(
			'message' => $message,
			'status' => $status
		);
	}

	/**
	 * 
	 */
	private function getReturnData($format = 'json')
	{
		if (!in_array($format, self::$validFormats)) {
			$format = 'json';
		}

		switch($format) {
			case 'json':
				return json_encode($this->returnData);
				break;
		}
	}
}