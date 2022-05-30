<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "SearchWordsModeRegistry.php";

class SearchWordsMode extends CacheableSet
{
	function __construct() {
		return parent::__construct( new SearchWordsModeRegistry($this) );
	}
}
