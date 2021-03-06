<?php
class MerchDataEntries extends AppModel
{
    public $name = 'MerchDataEntries';
    public $useTable = 'merchDataEntries';
    public $primaryKey = 'id';
    public $belongsTo = array(
        'MerchDataType' => array(
            'foreignKey' => 'merchDataTypeId'
        ),
        'MerchDataGroup' => array(
            'foreignKey' => 'merchDataGroupId'
        )
    );


    /**
     * @param mixed $results
     * @return mixed
     */
    public function afterFind($results)
    {
        foreach ($results AS &$r) {
            if (isset($r['MerchDataEntries']['merchDataJSON'])
                && ($merchDataArr = json_decode($r['MerchDataEntries']['merchDataJSON'], true)) != null
            ) {
                $r['MerchDataEntries']['merchDataArr'] = $merchDataArr;
            }
        }

        return $results;
    }

    /**
     * @return mixed
     */
    public function getEntriesForToday()
    {
        $date = date('Y-m-d');
        return $this->getEntriesByDate($date);

    }

    /**
     * @param $date
     * @return bool|mixed
     */
    public function getEntriesByDate($date)
    {
        $results = array();
        $options = array(
            'conditions' => array(
                'startDate' => $date
            )
        );

        if (!is_null($merchTypeId)) {
            $options['conditions']['merchDataTypeId'] = $merchTypeId;
        }

        $results = $this->find('all', $options);
        return (!empty($results)) ? $results : false;
    }

    /**
     * @param $entryId
     * @return bool
     */
    public function getEntryInfoByEntryId($entryId)
    {
        $results = array();
        $options = array(
            'conditions' => array(
                'MerchDataEntries.id' => $entryId
            )
        );

        $results = $this->find('first', $options);
        return (!empty($results)) ? $results : false;
    }
}
