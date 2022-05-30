<?php
include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include "WorkerUserRegistry.php";

class WorkerUser extends User
{
	function __construct() {
		parent::__construct( new WorkerUserRegistry($this) );
	}

    function getPage()
    {
        return defined('PERMISSIONS_ENABLED') && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant'))
            ? getSession()->getApplicationUrl($this).'module/permissions/participants?'
            : parent::getPage();
    }

    function IsDictionary() {
        return true;
    }
}