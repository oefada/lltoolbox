<?php
class ClientNotification extends AppModel
{
    public $name = 'ClientNotification';
    public $useTable = 'clientNotifications';

    /**
     * We don't want to save duplicate entries and double up on notifications
     * This is also enforced at the DB level with a unique index on clientId and merchDataEntryId
     *
     * @return bool
     */
    function beforeSave()
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
                unset($this->data);
            }
        }

        return parent::beforeSave();
    }
}
