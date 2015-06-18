<?php

include 'ResourceTable.php';
include "RoleUsageTable.php";
include "UserUsageTable.php";
include 'ResourceTasks.php';

class ResourcePage extends PMPage
{
	function getObject()
	{
 		return getFactory()->getObject('HumanResource');
	}
	
    function getTable()
    {
        if ( $_REQUEST['role'] > 0 )
        {
            return new RoleUsageTable($this->getObject());
        }
        
        if ( $_REQUEST['user'] > 0 )
        {
            return new UserUsageTable($this->getObject());
        }
        
        return new ResourceTable($this->getObject());
    }

    function export()
    {
        if ( ResourceTasks::export() ) return;
        
        parent::export();
    }
}
