<?php

class LltgServiceHelperComponent extends Object {

	public function getServiceBuilderFromTldId($tldId)
	{
		App::import("Vendor", "ServiceHelper", array('file' => "appshared".DS."helpers".DS."ServiceHelper.php"));
		App::import("Vendor", "ContextAwareInterface", array('file' => "appshared".DS."interfaces".DS."ContextAwareInterface.php"));
		App::import("Vendor", "ContextAwareService", array('file' => "appshared".DS."Services".DS."ContextAwareService.php"));
		$serviceBuilder = ServiceHelper::getServiceBuilderFromTldId($tldId);
		return $serviceBuilder;
	} 
	
	public function getCurrencyService($builder)
	{
		App::import("Vendor", "CurrencyService", array('file' => "appshared".DS."Services".DS."CurrencyService.php"));
		$currencyService = $builder->getService('CurrencyService');
		return $currencyService;
	}	
	
}
