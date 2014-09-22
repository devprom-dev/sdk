<?php

include_once SERVER_ROOT_PATH."cms/classes/model/events/BusinessTransactionAfterEventHandler.php";

abstract class WorklfowMovementEventHandler extends BusinessTransactionAfterEventHandler
{
	function readyToHandle()
	{ 
		return $this->getObjectIt()->object instanceof MetaobjectStatable;  
	}
}