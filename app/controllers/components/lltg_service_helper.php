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
}
