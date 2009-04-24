<?php
class MessageQueue extends AppModel {

	var $name = 'MessageQueue';
	var $order = 'created desc';
    var $displayField = 'title';
    
    function total($conditions) {
        $results = $this->find('first', array('conditions' => $conditions, 'fields' => array('COUNT(*) as total')));

        return $results[0]['total'];
    }
}
?>