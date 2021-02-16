<?php
include_once "TaskType.php";
include "TaskTypeBaseIterator.php";
include "TaskTypeBaseRegistry.php";

class TaskTypeBase extends MetaobjectCacheable 
{
    function __construct()
    {
        parent::__construct('pm_TaskType', new TaskTypeBaseRegistry($this));
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
        $this->setAttributeType('ProjectRole', 'REF_ProjectRoleBaseId');
        $this->setAttributeRequired('ParentTaskType', false);
        $this->setAttributeVisible('ParentTaskType', false);
        $this->setAttributeRequired('ReferenceName', true);
    }
    
	function createIterator() 
	{
		return new TaskTypeBaseIterator($this);
	}
 	
 	function IsVPDEnabled()
 	{
 		return false;
 	}
}