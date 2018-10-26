<?php

class TaskTag extends CustomTag
{
    function __construct()
    {
        parent::__construct();
        $this->setAttributeType('ObjectId', 'REF_TaskId');
    }
    
    function getObject() {
 		return getFactory()->getObject('Task');
 	}
 	
 	function resetFilters()
 	{
 	    parent::resetFilters();
 	    
 	    $this->addFilter(
 	            new FilterAttributePredicate('ObjectClass', strtolower(get_class($this->getObject())))
 	    );
 	}
}
