<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "UserParticipanceTypeRegistry.php";

class UserParticipanceType extends CacheableSet
{
	function __construct()
	{
		parent::__construct( new UserParticipanceTypeRegistry($this) );
	}
	
    function getDisplayName()
    {
        return translate('Пользователи');
    }
}