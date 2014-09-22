<?php

include 'SubversionForm.php';
include 'ConnectorTable.php';
include 'SubversionDebugTable.php';

class SubversionConnectorPage extends PMPage
{
    function getObject()
    {
        return getFactory()->getObject('pm_Subversion');
    }
 	
 	function getTable() 
 	{
 	    if ( $_REQUEST['connection'] > 0 )
 	    {
 		    return new SubversionDebugTable($this->getObject());
 	    }
 	    else
 	    {
 		    return new ConnectorTable($this->getObject());
 	    }
 	}
 	
 	function getForm() 
 	{
 		$object = $this->getObject();
 		
 		$object->addAttribute('Users', 'REF_SubversionUserId', text('sourcecontrol41'), true, false, text('sourcecontrol42'));
 		
 		return new SubversionForm($object);
 	}
}
