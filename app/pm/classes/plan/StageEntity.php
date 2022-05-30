<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "StageEntityRegistry.php";

class StageEntity extends CacheableSet
{
 	function __construct() {
 		parent::__construct(new StageEntityRegistry($this));
 	}
 	
 	function getDisplayName() {
 		return translate('Элементы плана');
 	}
}