<?php

include "TextChangeHistoryRegistry.php";

class TextChangeHistory extends Metaobject
{
	public function __construct()
	{
		parent::__construct('pm_TextChanges', new TextChangeHistoryRegistry($this));
	}
}