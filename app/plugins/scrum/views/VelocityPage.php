<?php

include "VelocityTable.php";

class VelocityPage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();
 	}
 	
	function getObject()
	{
	    global $model_factory;
	    
	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
	    
	    if ( $methodology_it->HasPlanning() )
	    {
	        getSession()->addBuilder( new IterationMetadataVelocityBuilder() );
	        
	        $object = $model_factory->getObject('Iteration');
	    }
	    else 
	    {
	        getSession()->addBuilder( new ReleaseMetadataVelocityBuilder() );
	        
	        $object = $model_factory->getObject('Release');
	    }
 		
 		return $object;
	}
	
 	function getTable() 
 	{
 		return new VelocityTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
}