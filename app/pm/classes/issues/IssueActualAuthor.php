<?php

include "IssueActualAuthorRegistry.php";

class IssueActualAuthor extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('cms_User', new IssueActualAuthorRegistry());
 	}
}