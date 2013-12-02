<?php
uses('sanitize');
App::import('Model', 'MessageQueue');
class AppController extends Controller
{
    public $helpers = array(
        'Html2',
        'Html',
        'Form',
        'Text',
        'Pagination',
        'Layout',
        'Ajax',
        'StrictAutocomplete',
        'Number',
        'DatePicker',
        'Prototip',
        'Session',
        'Multisite',
        'Utilities'
    );
    public $components = array('Acl', 'LdapAuth', 'RequestHandler', 'DebugKit.Toolbar');
    public $publicControllers = array('sessions');
    public $Sanitize;

    protected $securedUsers = array('mclifford', 'cholland', 'kferson', 'mtrinh', 'emendoza');

    /**
     *
     */
    public function beforeFilter()
    {
        if (isset($this->LdapAuth)) {
            $this->LdapAuth->loginAction = array('controller' => 'sessions', 'action' => 'login');
            $this->LdapAuth->loginError = "Could not log you in, please try again.";
            $this->LdapAuth->authError = "Insufficient access rights.<br />Must be logged in, or logged in with elevated access.";
            $this->LdapAuth->userModel = 'AdminUser';
            $this->LdapAuth->authorize = 'controller';

            if (in_array(low($this->params['controller']), $this->publicControllers)) {
                $this->LdapAuth->allow();
            }

            $user = $this->LdapAuth->user();
            $this->user = $user;
            $this->set('user', $user);
            $this->set('userDetails', $user['LdapUser']);
        }

        $this->Sanitize = new Sanitize();

        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', '0');
        }

        $this->RequestHandler->setContent('json', 'text/x-json');

        if ($this->RequestHandler->prefers('pdf') || $this->RequestHandler->prefers('doc')) {
            error_reporting(E_ERROR);
            Configure::write('debug', '0');
        }

        if (isset($this->{$this->modelClass}) && is_object($this->{$this->modelClass}) &&
            isset($this->{$this->modelClass}->Behaviors) &&
            $this->{$this->modelClass}->Behaviors->attached('Logable')
        ) {
            $this->{$this->modelClass}->setUserData($user);
            $this->{$this->modelClass}->setUserIp($this->_userIp());
        }

        if ($user) {
            $messageQueue = new MessageQueue;

            $unread = $messageQueue->total(array('toUser' => $user['LdapUser']['username'], 'read <>' => 1));
            $severity = $messageQueue->total(
                array('toUser' => $user['LdapUser']['username'], 'read <>' => 1, 'severity' => 3)
            );

            $this->set('queueCountUnread', $unread);
            $this->set('queueCountSeverity', $severity);
        }

        $this->set('sites', array('luxurylink' => 'Luxury Link', 'family' => 'Family'));
        $this->set('jsVersion', '20131101');
        $this->siteIds = array(1 => 'Luxury Link', 2 => 'Family');
        $this->siteDbs = array(1 => 'luxurylink', 2 => 'family');
        $this->set('siteIds', $this->siteIds);
        $this->_defineConstants();
    }

    /**
     * Method used to define constants for things we use repeatedly. Examples are the ID's for certain offer types.
     */
    public function _defineConstants()
    {
        define('OFFER_TYPES_FIXED_PRICED', serialize(array(3, 4)));
        define('OFFER_TYPES_AUCTION', serialize(array(1, 2, 6)));
    }

    /**
     * @return mixed
     */
    public function _userIp()
    {
        if (@$_SERVER['HTTP_X_FORWARD_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function isAuthorized()
    {
        return true;
    }

    /**
     * @param $username
     * @return bool
     */
    protected function isSecuredUser($username)
    {
        return in_array($username, $this->securedUsers);
    }
}
