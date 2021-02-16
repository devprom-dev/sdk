<?php

include "WatchingsTable.php";

class WatchingsPage extends PMPage
{
 	function WatchingsPage()
 	{
 		parent::PMPage();
 	}

 	function getTable() 
 	{
 		return new WatchingsTable();
 	}
 	
 	function getEntityForm()
 	{
 		return null;
 	}
}
