<?php
class NotifyClientsShell extends Shell
{
    /**
     * @var Controller $controller
     */
    private $Controller;

    /**
     * @var EmailComponent $Email
     */
    private $Email;

    /**
     * @var Client $clientModel
     */
    private $clientModel;

    /**
     * @var ClientNotification $clientNotificationModel
     */
    private $clientNotificationModel;

    /**
     * @var array $errors
     */
    private $errors = array();

    public function __destruct()
    {
        if ($this->getErrors() !== false) {
            exit(1);
        }
    }

    /**
     * Overriding this to mute the welcome display
     */
    public function _welcome() {}

    public function initialize()
    {
        Configure::write('Cache.disable', true);
        ini_set('session.save_handler', 'files');
        App::import('Core', 'Controller');
        App::import('Component', 'Email');
        APP::import('Model', 'Client');
        APP::import('Model', 'ClientNotification');
        $this->Controller = new Controller();
        $this->Email = new EmailComponent();
        $this->Email->initialize($this->Controller);
        $this->clientModel = new Client();
        $this->clientNotificationModel = new ClientNotification();
    }

    public function main()
    {
        $clientsToNotify = $this->clientNotificationModel->getClientsToNotify();
        if($clientsToNotify !== false) {
            foreach ($clientsToNotify as $client) {
                $client = $client['ClientNotification'];
                $this->out("Attempting to send notifications for clientId {$client['clientId']}, merchDataEntryId {$client['merchDataEntryId']}");

                $contactDetails = $this->clientModel->getHomepageContact($client['clientId']);
                if ($contactDetails !== false) {
                    $clientName = $contactDetails[0]['client_name'];
                    $accountManagerEmail = $contactDetails[0]['account_manager_email'];
                    $contactEmail = array($accountManagerEmail);

                    $this->out("    $clientName was featured on our site.");
                    $this->out('    Notifications will be sent to the following addresses: ');
                    $this->out('        ' . $accountManagerEmail);
                    foreach($contactDetails as $contact) {
                        $contactEmail[] = $contact['contact_email'];
                        $this->out('        ' . $contact['contact_email']);
                    }
                    $contactEmail[] = "mclifford@luxurylink.com";

                    if ($this->emailReport($clientName, $contactEmail, $accountManagerEmail) !== false) {
                        $this->clientNotificationModel->id = $client['id'];
                        $this->clientNotificationModel->saveField('notified', date('Y-m-d H:i:s'));
                    } else {
                        $this->setErrors('There was a problem sending the notification.');
                    }
                } else {
                    $this->setErrors('There were no contacts found.');
                }
            }
        } else {
            $this->out('There are no clients to notify.');
        }
    }

    /**
     * @return array|bool
     */
    private function getErrors()
    {
        return (!empty($this->errors)) ? $this->errors : false;
    }
    /**
     * @param $message
     */
    private function setErrors($message)
    {
        $this->errors[] = $message;
        $this->out($message);
    }

    /**
     * @param array $contactEmail
     * @param string $clientName
     * @param string $accountManagerEmail
     * @return bool
     */
    private function emailReport($clientName, $contactEmail, $accountManagerEmail)
    {
        $this->Email->reset();
        $this->Email->from = 'Luxury Link Travel Group <clientmarketing@luxurylink.com>';
        $this->Email->to = 'Luxury Link Travel Group <noreply@luxurylink.com>';
        $this->Email->cc = array($accountManagerEmail);
        $this->Email->bcc = $contactEmail;
        $this->Email->subject = "$clientName is being featured on Luxury Link";
        $this->Email->template = 'client_notifications_email';
        $this->Email->sendAs = 'text';
        $this->Controller->set('accountManagerEmail', $accountManagerEmail);
        $this->Controller->set('clientName', $clientName);
        return $this->Email->send();
    }
}
