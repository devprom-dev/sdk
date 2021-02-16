<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

class WikiFilterActualLinkWebMethod extends FilterWebMethod
{
 	function getCaption() {
 		return text(1043);
 	}

 	function getValues() {
  		return array (
 			'all' => text(2248),
 			'actual' => text(2249),
 			'nonactual' => text(2250),
            'empty' => text(2251)
 			);
	}

	function getStyle() {
		return 'width:110px;';
	}

 	function getValueParm() {
 		return 'linkstate';
 	}

 	function getType() {
 		return 'singlevalue';
 	}
}
