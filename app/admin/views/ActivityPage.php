<?php

include "ActivityTable.php";

class ActivityPage extends AdminPage
{
 	function getTable() 
 	{
 		return new ActivityTable(getFactory()->getObject('AdminChangeLog'));
 	}

    function getEntityForm()
 	{
 		return null;
 	}
}
