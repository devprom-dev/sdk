<?php

include "ProjectStateRegistry.php";

class ProjectState extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('entity', new ProjectStateRegistry($this));
 	}
 	
 	function getDisplayName()
 	{
 		return translate('Состояние');
 	}
}