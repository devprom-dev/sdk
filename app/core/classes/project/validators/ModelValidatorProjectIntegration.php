<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorProjectIntegration extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
	    if ( $parms['DemoData'] != 'I' ) return '';
	    if ( !class_exists(getFactory()->getClass('IntegrationTracker')) ) return '';

	    $trackerIt = getFactory()->getObject('IntegrationTracker')->getAll();
	    if ( $parms['Tracker'] == '' || !in_array($parms['Tracker'], $trackerIt->fieldToArray('entityId')) ) return text(2326);

		return "";
	}
}