<?php

if ( !class_exists('CustomTag', false) ) include "CustomTag.php";

class QuestionTag extends CustomTag
{
    function __construct()
    {
        parent::__construct();
        $this->setAttributeType('ObjectId', 'REF_QuestionId');
    }
    
 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Question');
 	}

 	function resetFilters()
 	{
 	    parent::resetFilters();
 	    
 	    $this->addFilter(
 	            new FilterAttributePredicate('ObjectClass', strtolower(get_class($this->getObject())))
 	    );
 	}
}
