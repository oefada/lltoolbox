<?php
class LoaItemDate extends AppModel {

	var $name = 'LoaItemDate';
	var $useTable = 'loaItemDate';
	var $primaryKey = 'loaItemDateId';
	
	var $belongsTo = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'));
    
    function updateFromPackage($data, $packageId, $loaItemRatePeriodId) {
        foreach($data as &$d) {
            $d['loaItemRatePeriodId'] = $loaItemRatePeriodId;
            $d['startDate'] = date('Y-m-d', strtotime($d['startDate']));
            $d['endDate'] = date('Y-m-d', strtotime($d['endDate']));
        }
        //debug($data);
        //die();
        if ($this->saveAll($data)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function getValidDates($loaItemId) {
        $query = "SELECT startDate, endDate
                  FROM loaItemDate LoaItemDate
                  INNER JOIN loaItemRatePeriod USING (loaItemRatePeriodId)
                  WHERE loaItemId = {$loaItemId}";
        $validities = $this->query($query);
        $dates = array();
        foreach ($validities as $i => $v) {
            $dates[$i]['startDate'] = $v['LoaItemDate']['startDate'];
            $dates[$i]['endDate'] = $v['LoaItemDate']['endDate'];
        }
        return $dates;
    }
    
    
    function deleteFromPackage($data) {
        foreach ($data as $date) {
            if (!empty($date['loaItemDateId'])) {
                $this->delete($date['loaItemDateId']);
            }
        }
    }
    
}
?>
