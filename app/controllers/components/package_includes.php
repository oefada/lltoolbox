<?php
/**
 * Strips promo info from package includes
 * 
 * @author Rob Vella
 */
class PackageIncludesComponent extends Object {
	public function removePromoInfo(&$package, $type = 'package') {
		$field = $this->getField($type);

		if ($field === false || !isset($package[$field])) {
			return;
		}
		
		if (preg_match("~\<promo\>(.*?)\</promo\>~si", $package[$field], $matches)) {
			$package[$field] = str_replace($matches[0],'', $package[$field]);
		}
		
		return;
	}
	
	private function getField($type)
	{
		$field = false;
		
		if ($type == 'package') {
			$field = "packageIncludes";
		} elseif ($type == 'offer') {
			$field = "offerIncludes";
		}
		
		return $field;
	} 
}
