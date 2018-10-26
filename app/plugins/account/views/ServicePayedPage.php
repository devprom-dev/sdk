<?php

include "ServicePayedForm.php";
include "ServicePayedTable.php";
        
class ServicePayedPage extends AdminPage
{
	function getObject()
	{
		return getFactory()->getObject('ServicePayed');
	}
	
    function getTable()
    {
        return new ServicePayedTable($this->getObject());
    }

    function getForm()
    {
        return new ServicePayedForm($this->getObject());
    }
}
