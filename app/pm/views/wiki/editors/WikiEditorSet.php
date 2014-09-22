<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "WikiEditorSetRegistry.php";

class WikiEditorSet extends CacheableSet
{
	public function __construct()
	{
		parent::__construct( new WikiEditorSetRegistry($this) );
	}
}
