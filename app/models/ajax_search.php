<?php
class AjaxSearch extends AppModel {
    var $name = 'AjaxSearch';
	var $useTable = false;
	var $primaryKey = false;

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