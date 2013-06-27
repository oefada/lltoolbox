<?php

class LltgServiceHelperComponent extends Object {

	public function getServiceBuilderFromTldId($tldId)
	{
		App::import("Vendor", "ContextInterface", array('file' => "appshared".DS."interfaces".DS."ContextInterface.php"));
		App::import("Vendor", "ServiceInterface", array('file' => "appshared".DS."interfaces".DS."ServiceInterface.php"));
		App::import("Vendor", "ServiceHelper", array('file' => "appshared".DS."helpers".DS."ServiceHelper.php"));
		App::import("Vendor", "ContextAwareInterface", array('file' => "appshared".DS."interfaces".DS."ContextAwareInterface.php"));
		App::import("Vendor", "ContextAwareService", array('file' => "appshared".DS."Services".DS."ContextAwareService.php"));
		$serviceBuilder = ServiceHelper::getServiceBuilderFromTldId($tldId);
		return $serviceBuilder;
	} 
	
	public function getCurrencyService($builder)
	{
		App::import("Vendor", "CurrencyService", array('file' => "appshared".DS."Services".DS."CurrencyService.php"));
		return $builder->getService('CurrencyService');
	}	

	public function getComponentService($builder)
	{
		App::import("Vendor", "ComponentService", array('file' => "appshared".DS."Services".DS."ComponentService.php"));
		return $builder->getService('ComponentService');
	}
	
}
