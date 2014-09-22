<?php

include "WikiPageVersionRegistry.php";

class WikiPageVersion extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Snapshot', new WikiPageVersionRegistry($this) );
	}
	
	public function getDisplayName()
	{
		return translate('Версия');
	}
	
	public function getObjectClass()
	{
		return '';
	}
}