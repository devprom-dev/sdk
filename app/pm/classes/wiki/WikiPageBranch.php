<?php

include "WikiPageBranchRegistry.php";
include "predicates/WikiPageBranchFilter.php";

class WikiPageBranch extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Snapshot', new WikiPageBranchRegistry($this) );
	}
	
	public function getDisplayName()
	{
		return translate('Бейзлайн');
	}
	
	public function getObjectClass()
	{
		return '';
	}
}