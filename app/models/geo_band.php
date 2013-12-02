<?php
class GeoBand extends AppModel {

	var $name = 'GeoBand';
	var $useTable = 'geoBand';
	var $primaryKey = 'geoBandId';
	var $displayField = 'description';

	var $hierarchy;
							 
	function getHierarchy() {
		if ($this->hierarchy == null) {
			$this->recursive = -1;
			$result = $this->find('all', array('order'=>array('description')));

			$hierarchy = array();
			foreach($result as $r) {
				$b = $r['GeoBand'];
				$b['children'] = array();
				$b['parentId'] = intval($b['parentId']);
				$hierarchy[$b['geoBandId']] = $b;
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
				$result .= $this->getHierarchySelectRecursive($h['geoBandId'], $selected);
			}
		}
		return $result;
	}
								 
	function getHierarchySelectRecursive($id, $selected, $str = '', $count = 0) {
		$hierarchy = $this->getHierarchy();
		$band = $hierarchy[$id];
		$str .= '<option value="' . $band['geoBandId'] . '"';
		if ($id == $selected) { $str .= ' selected'; }
		$str .= '>';
		$loop = 0;
		while ($loop < $count) {
			$loop++;
			$str .= '--- ';
		}
		$str .= $band['description'] . '</option>';
		$count++;
		foreach($band['children'] as $c) {
			$arr[] = $c;
			$str = $this->getHierarchySelectRecursive($c, $selected, $str, $count);
		}	
		return $str;
	}
								 
	function getLineageForId($id) {
		$hierarchy = $this->getHierarchy();
		$band = $hierarchy[$id];
		$result = array($dest);
		while($band['parentId'] > 0) {
			$band = $hierarchy[$band['parentId']];
			$result[] = $band;
		}
		return array_reverse($result, true);
	}



								 
}
?>