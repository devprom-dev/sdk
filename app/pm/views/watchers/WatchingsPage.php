<?php
include "WatchingsTable.php";

class WatchingsPage extends PMPage
{
 	function getTable() {
 		return new WatchingsTable();
 	}
 	
 	function getEntityForm() {
 		return null;
 	}
}
