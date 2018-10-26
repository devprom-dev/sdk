<?php
include_once "StateBase.php";

class RequestState extends StateBase
{
 	function getObjectClass()
 	{
 		return 'issue';
 	}
 	
 	function getDisplayName()
 	{
 		return text(2651);
 	}
}
