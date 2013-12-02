<?php
class AjaxSearch extends AppModel {
    var $name = 'AjaxSearch';
	var $useTable = false;
	var $primaryKey = false;

	public function tableChanged() {
		$table = $this->table;
		if ((time() - Cache::read('AjaxCacheTime_'.$table)) >= 300) {
			$config = ConnectionManager::getDataSource('default');
			$config = $config->config;

			$prevInfo = Cache::read('AjaxCacheData_'.$table);
			$prevInfo = is_object($prevInfo) ? $prevInfo : (object)array();
			
			$updateInfo = $this->query("SELECT UPDATE_TIME,ENGINE,AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='".$config['database']."' AND TABLE_NAME='".$table."'");
			$updateInfo = (object)$updateInfo[0]['TABLES'];
			
			$update = 0;
			
			if ($updateInfo->UPDATE_TIME != NULL) {
				if ($prevInfo->UPDATE_TIME != $updateInfo->UPDATE_TIME) $update = 1;
			} elseif ($updateInfo->ENGINE == "InnoDB") {
				// Changed if auto increment is different
				if ($prevInfo->AUTO_INCREMENT != $updateInfo->AUTO_INCREMENT) $update = 1;
			}
			
			if ($update) {
				Cache::write('AjaxCacheTime_'.$table,time());
				Cache::write('AjaxCacheData_'.$table,$updateInfo);
				
				return true;
			}
			
			return false;
		} else {
			return false;
		}
	}
	
	public function cacheClientNames() {
		if ((time() - Cache::read('clientNamesChecksumTime')) >= 300) {
			$checksum = $this->query("CHECKSUM TABLE client");
			$checksum = $checksum[0][0]['Checksum'];
			
			if ($checksum != Cache::read('clientNamesChecksum')) {
				Cache::write('clientNamesChecksum',$checksum);
				$this->query("TRUNCATE TABLE clientNames");
				$this->query("INSERT INTO clientNames (`name`,`clientId`) SELECT `AjaxSearch`.`name`, `AjaxSearch`.`clientId` FROM `client` AS `AjaxSearch` INNER JOIN `clientSiteExtended` AS `ClientSiteExtendedSlim` ON (`ClientSiteExtendedSlim`.`inactive` = 0 AND `ClientSiteExtendedSlim`.`clientId` = `AjaxSearch`.`clientId`) GROUP BY clientId");
			}
			
			Cache::write('clientNamesChecksumTime',time());
		}
	}
}

?>