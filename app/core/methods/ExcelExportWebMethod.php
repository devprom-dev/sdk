<?php
include_once "ExportWebMethod.php";

class ExcelExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return text(2201);
 	}
 	
 	function url( $caption = '', $class = 'IteratorExportExcel', $parms = array() )
 	{
 		return parent::getJSCall(
			array_merge(
				array(
					'caption' => \TextUtils::stripAnyTags($caption != '' ? $caption : $this->getCaption()),
					'class' => $class
				),
				$parms
			)
		);
 	}
 	
 	function execute_request()
 	{
 		parent::execute_request();

 		unset($_POST['id']);
        unset($_POST['object']);
        unset($_POST['objects']);

 		echo '&'.http_build_query(
				array_map(
					function($value) {
						return SanitizeUrl::parseUrl($value);
					},
					$_POST
				)
			);
 	}
}
