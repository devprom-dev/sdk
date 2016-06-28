<?php

include_once "WikiPage.php";
include "PMWikiPageIterator.php";
include "predicates/PMWikiStageFilter.php";
include "predicates/PMWikiLinkedStateFilter.php";
include "predicates/PMWikiSourceFilter.php";
include "predicates/WikiRelatedIssuesPredicate.php";

class PMWikiPage extends WikiPage 
{
	function createIterator() 
	{
		return new PMWikiPageIterator($this);
	}
	
	function IsStatable()
	{
		return count(WorkflowScheme::Instance()->getStates($this)) > 0;
	}
	
 	function getStateClassName()
 	{
		return '';
 	}

	function getTypeIt()
	{
		return null;
	}
	
	function getPage()
	{
	}
	
	function getPageHistory()
	{
	}
	
	function getAttributeObject( $attr )
	{
		switch ( $attr )
		{
			case 'ParentPage':
				return $this;
				
			default:
				return parent::getAttributeObject( $attr );
		}
	}
}