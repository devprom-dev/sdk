<?php

include "IssueAuthorRegistry.php";

class IssueAuthor extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('cms_User', new IssueAuthorRegistry());
 	}
}