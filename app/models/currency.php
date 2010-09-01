<?php
class Currency extends AppModel {

	var $name = 'Currency';
	var $useTable = 'currency';
	var $primaryKey = 'currencyId';
	var $displayField = 'currencyName';
    
    function getPackageCurrencyCode($packageId) {
        $query = "SELECT currencyCode FROM currency Currency
                  INNER JOIN package Package USING (currencyId)
                  WHERE Package.packageId = {$packageId}";
        if ($currencyCode = $this->query($query)) {
            return $currencyCode[0]['Currency']['currencyCode'];
        }
        else {
            return false;
        }
    }

	function getPackageCurrencyId($packageId) {
		$result = $this->query("SELECT currencyId FROM package WHERE packageId = $packageId");
		return $result[0]['package']['currencyId'];
	}
}
?>
