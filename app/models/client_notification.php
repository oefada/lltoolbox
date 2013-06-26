<?php
class ClientNotification extends AppModel
{
    public $name = 'ClientNotification';
    public $useTable = 'clientNotifications';

    private $warnings = array();

    /**
     * We don't want to save duplicate entries and double up on notifications
     * This is also enforced at the DB level with a unique index on clientId and merchDataEntryId
     *
     * @return bool
     */
    public function beforeSave()
    {
        if (
            isset($this->data['ClientNotification']['clientId'])
            && isset($this->data['ClientNotification']['merchDataEntryId'])
        ) {
            $options = array(
                'conditions' => array(
                    'clientId' => $this->data['ClientNotification']['clientId'],
                    'merchDataEntryId' => $this->data['ClientNotification']['merchDataEntryId']
                )
            );

            if ($this->find('first', $options) !== false) {
                $this->setWarnings(
                    "Duplicate entry for clientId: {$this->data['ClientNotification']['clientId']}, merchDataEntryId: {$this->data['ClientNotification']['merchDataEntryId']}"
                );
                unset($this->data);
            }
        }

        return parent::beforeSave();
    }

    /**
     * @return array|bool
     */
    public function getWarnings()
    {
        return (empty($this->warnings)) ? false : $this->warnings;
    }

    /**
     *
     */
    public function resetWarnings()
    {
        $this->warnings = array();
    }

    /**
     * @param $message
     */
    public function setWarnings($message)
    {
        $this->warnings[] = $message;
    }
}
