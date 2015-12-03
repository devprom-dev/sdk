<?php

include_once SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";

include "FunctionForm.php";
include "FunctionTable.php";
include "PageSettingFeatureBuilder.php";

class FunctionsPage extends PMPage
{
 	function FunctionsPage()
 	{
 	    getSession()->addBuilder( new PageSettingFeatureBuilder() );
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
 		    $object_it = $this->getObjectIt();
 		    if( is_object($object_it) && $object_it->getId() > 0 ) {
	            $this->addInfoSection( new PageSectionComments($object_it) );
 		    }
 		}
 	}
 	
 	function getObject()
 	{
 		$object = getFactory()->getObject('Feature');

		$builder = new FeatureModelExtendedBuilder();
		$builder->build($object);

		return $object;
 	}
 	
 	function getTable() 
 	{
		return new FunctionTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
		return new FunctionForm( $this->getObject() );
 	}
}