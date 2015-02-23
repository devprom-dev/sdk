<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include "ProjectUserRegistry.php";

class ProjectUser extends User
{
	function __construct()
	{
		parent::__construct( new ProjectUserRegistry($this) );
	}
}