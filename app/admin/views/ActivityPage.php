<?php

include "ActivityTable.php";

class ActivityPage extends AdminPage
{
 	function getTable() 
 	{
 		return new ActivityTable();
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
}
