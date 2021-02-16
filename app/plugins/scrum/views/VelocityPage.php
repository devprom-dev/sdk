<?php
include "VelocityTable.php";
include SERVER_ROOT_PATH . "plugins/scrum/classes/ReleaseModelVelocityBuilder.php";
include SERVER_ROOT_PATH . "plugins/scrum/classes/IterationModelVelocityBuilder.php";

class VelocityPage extends PMPage
{
 	function __construct() {
 		parent::__construct();
 	}
 	
	function getObject()
	{
	    if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() ) {
	        getSession()->addBuilder( new IterationModelVelocityBuilder() );
	        $object = getFactory()->getObject('Iteration');
	    }
	    else {
	        getSession()->addBuilder( new ReleaseModelVelocityBuilder() );
	        $object = getFactory()->getObject('Release');
	    }
        $object->addAttribute('Velocity', 'INTEGER', translate('Скорость'), true);
 		return $object;
	}
	
 	function getTable() 
 	{
 		return new VelocityTable( $this->getObject() );
 	}
 	
 	function getEntityForm()
 	{
 		return null;
 	}
}