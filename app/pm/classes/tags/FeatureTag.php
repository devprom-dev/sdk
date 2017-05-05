<?php

if ( !class_exists('CustomTag', false) ) include "CustomTag.php";

class FeatureTag extends CustomTag
{
    function __construct()
    {
        parent::__construct();
        $this->setAttributeType('ObjectId', 'REF_FeatureId');
    }
    
    function getObject()
 	{
 		return getFactory()->getObject('Feature');
 	}
 	
 	function resetFilters()
 	{
 	    parent::resetFilters();
 	    
 	    $this->addFilter(
 	            new FilterAttributePredicate('ObjectClass', strtolower(get_class($this->getObject())))
 	    );
 	}
}
