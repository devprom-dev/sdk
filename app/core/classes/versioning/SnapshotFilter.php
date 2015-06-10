<?php

include "SnapshotFilterRegistry.php";

class SnapshotFilter extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Snapshot', new SnapshotFilterRegistry());
	}
	
	function getDisplayName()
	{
		return translate('Версия');
	}
}