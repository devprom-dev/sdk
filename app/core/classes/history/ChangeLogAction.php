<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "ChangeLogActionRegistry.php";

class ChangeLogAction extends CacheableSet
{
	function __construct()
	{
		parent::__construct( new ChangeLogActionRegistry($this) );
	}
	
    function getDisplayName()
    {
        return translate('Активность');
    }
}