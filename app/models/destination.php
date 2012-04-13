<?php
class Destination extends AppModel {

	var $name = 'Destination';
	var $useTable = 'destination';
	var $primaryKey = 'destinationId';
	var $displayField = 'destinationName';

	var $hierarchy;
	
	var $belongsTo = array('Tag' => array('foreignKey' => 'tagId'));
	var $hasAndBelongsToMany = array('Client' => array('foreignKey' => 'destinationId',
							 		   'associationForeignKey' => 'clientId',
									   'with' => 'ClientDestinationRel'										
								 ));
								 
	function getHierarchy() {
		if ($this->hierarchy == null) {
			$this->recursive = -1;
			$result = $this->find('all', array('order'=>array('destinationName')));

			$hierarchy = array();
			foreach($result as $r) {
				$d = $r['Destination'];
				$d['children'] = array();
				$d['parentId'] = intval($d['parentId']);
				$hierarchy[$d['destinationId']] = $d;
			}
			foreach($hierarchy as $k=>$v) {
				if ($v['parentId'] > 0) {
					$hierarchy[$v['parentId']]['children'][] = $k;
				}
			}
			$this->hierarchy = $hierarchy;
		}
		return $this->hierarchy;
	}
	
	function getHierarchySelectOptions($selected) {
		$result = '';
		$hierarchy = $this->getHierarchy();
		foreach($hierarchy as $h) {
			if ($h['parentId'] == 0) {
				$result .= $this->getHierarchySelectRecursive($h['destinationId'], $selected);
			}
		}
		return $result;
	}
								 
	function getHierarchySelectRecursive($id, $selected, $str = '', $count = 0) {
		$hierarchy = $this->getHierarchy();
		$dest = $hierarchy[$id];
		$str .= '<option value="' . $dest['destinationId'] . '"';
		if ($id == $selected) { $str .= ' selected'; }
		$str .= '>';
		$loop = 0;
		while ($loop < $count) {
			$loop++;
			$str .= '--- ';
		}
		$str .= $dest['destinationName'] . '</option>';
		$count++;
		foreach($dest['children'] as $c) {
			$arr[] = $c;
			$str = $this->getHierarchySelectRecursive($c, $selected, $str, $count);
		}	
		return $str;
	}
								 
	function getLineageForId($id) {
		$hierarchy = $this->getHierarchy();
		$dest = $hierarchy[$id];
		$result = array($dest);
		while($dest['parentId'] > 0) {
			$dest = $hierarchy[$dest['parentId']];
			$result[] = $dest;
		}
		return array_reverse($result, true);
	}
								 
}
?>