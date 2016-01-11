<?php

include "IssueAuthorRegistry.php";

class IssueAuthor extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('cms_User', new IssueAuthorRegistry());
		$this->addAttributeGroup('Email', 'alternative-key');
 	}
 	
 	function getExact($id)
 	{
 		return $this->getRegistry()->Query(
 				array (
 						new FilterInPredicate($id)
 				)
 		);
 	}
}