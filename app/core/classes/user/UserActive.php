<?php

include "UserActiveRegistry.php";
		
class UserActive extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_User', new UserActiveRegistry());
	}
}