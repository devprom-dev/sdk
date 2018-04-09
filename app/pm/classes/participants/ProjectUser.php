<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include "ProjectUserIterator.php";
include "ProjectUserRegistry.php";

class ProjectUser extends User
{
	function __construct() {
		parent::__construct( new ProjectUserRegistry($this) );
	}

    function getPage()
    {
        return defined('PERMISSIONS_ENABLED') && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant'))
            ? getSession()->getApplicationUrl($this).'module/permissions/participants?'
            : parent::getPage();
    }

    function createIterator() {
        return new ProjectUserIterator($this);
    }
}