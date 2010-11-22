<?php
class LoaItemDate extends AppModel {

	var $name = 'LoaItemDate';
	var $useTable = 'loaItemDate';
	var $primaryKey = 'loaItemDateId';
	
	var $belongsTo = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'));
    
    function updateFromPackage($data, $loaItems, $packageId) {
        foreach($loaItems as $item) {
            foreach($data as $itemDate) {
                $loaItemDate = array('loaItemRatePeriodId' => $item['LoaItemRate'][0]['loaItemRatePeriodId'],
                                     'startDate' => date('Y-m-d', strtotime($itemDate['startDate'])),
                                     'endDate' => date('Y-m-d', strtotime($itemDate['endDate']))
                                    );
                $this->create();
                $saved = $this->save($loaItemDate);
                if (!$saved) return false;
            }
        }
        return true;
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
