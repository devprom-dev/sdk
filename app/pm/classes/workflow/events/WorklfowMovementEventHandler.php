<?php

include_once SERVER_ROOT_PATH."cms/classes/model/events/BusinessTransactionAfterEventHandler.php";

abstract class WorklfowMovementEventHandler extends BusinessTransactionAfterEventHandler
{
	function readyToHandle()
	{
	    if ( !is_object($this->getObjectIt()) ) return false;
		return $this->getObjectIt()->object instanceof MetaobjectStatable;  
	}
}