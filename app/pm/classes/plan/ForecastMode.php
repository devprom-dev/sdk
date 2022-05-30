<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "ForecastModeRegistry.php";

class ForecastMode extends CacheableSet
{
 	function __construct() {
 		parent::__construct(new ForecastModeRegistry($this));
 	}
 	
 	function getDisplayName() {
 		return translate('Прогноз');
 	}
}