<?php

class AffectedObjects extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		parent::__construct('co_AffectedObjects');
 	}

 	function getNotificationEnabled()
 	{
 	    return false;
 	}
}