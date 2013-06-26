<?php
class MerchDataEntries extends AppModel
{
    public $name = 'MerchDataEntries';
    public $useTable = 'merchDataEntries';
    public $primaryKey = 'id';
    public $belongsTo = array(
        'MerchDataType' => array(
            'foreignKey' => 'merchDataTypeId'
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
        $date = '2012-10-22';
        return $this->getEntriesByDate($date);

    }

    /**
     * @param $date
     * @return bool|mixed
     */
    private function getEntriesByDate($date)
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
}
