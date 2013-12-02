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




	public function flattenHierarchy($hierarchy) {
		$result = array();
		foreach($hierarchy as $h) {
			if ($h['parentId'] == 0) {
				$result = $this->flattenHierarchyRecursive($hierarchy, $h['destinationId'], $result);
			}
		}
		return $result;
	}

	private function flattenHierarchyRecursive($arr, $id, $result, $level = 0) {
		$dest = $arr[$id];
		$dest['level'] = $level;
		$level++;
		$result[$id] = $dest;
		if (isset($arr[$id]['children'])) {
			foreach($arr[$id]['children'] as $c) {
				$result = $this->flattenHierarchyRecursive($arr, $c, $result, $level);
			}
		}
		return $result;
	}
			
			
	public function getHierarchyWithBookingTotals($data) {
		$hierarchy = $this->getHierarchy();
		$hierarchy[0] = array('destinationId'=>0, 'parentId'=>0, 'destinationName'=>'No Destination Set', 'children'=>array());
		foreach($hierarchy as $k=>$v) {
			$hierarchy[$k]['locations'] = array();
		}
		
		foreach($data as $row) {
			$d = $row[0];
			$hierarchy[$d['destinationId']]['locations'][$d['locationDisplay']]['clients'][] = $d;
		}
		
		// set location totals
		foreach($hierarchy as $hk=>$hv) {
			foreach($hv['locations'] as $lk=>$lv) {
				$bTotal = 0;
				$bCount = 0;
				foreach($lv['clients'] as $l) {
					$bCount += $l['bookingCount'];
					$bTotal += $l['bookingTotal'];
				}
				$hv['locations'][$lk]['bookings'] = array('bookingCount'=>$bCount, 'bookingTotal'=>$bTotal);
			}
			$hierarchy[$hk]['locations'] = $hv['locations'];
		}
								
		foreach($hierarchy as $k=>$v) {
			$locationInfo = $this->getBookingsRecursiveInfo($hierarchy, $k);
			$hierarchy[$k]['clientCount'] = $locationInfo['clientCount'];
			$hierarchy[$k]['bookings'] = array('bookingCount'=>$locationInfo['bookingCount'], 'bookingTotal'=>$locationInfo['bookingTotal']);
		}		
		return $hierarchy;
	}

	private function getBookingsRecursiveInfo($arr, $id, $info = array('clientCount'=>0, 'bookingCount'=>0, 'bookingTotal'=>0)) {
		foreach($arr[$id]['locations'] as $l) {
			$info['clientCount'] += sizeof($l['clients']);
			$info['bookingCount'] += $l['bookings']['bookingCount'];
			$info['bookingTotal'] += $l['bookings']['bookingTotal'];
		}
		if (isset($arr[$id]['children'])) {
			foreach($arr[$id]['children'] as $c) {
				$info = $this->getBookingsRecursiveInfo($arr, $c, $info);
			}
		}
		return $info;
	}


								 
}
?>