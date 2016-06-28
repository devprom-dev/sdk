<?php

include_once "ExportWebMethod.php";

class ExcelExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return translate('Экспорт в Excel');
 	}
 	
 	function url( $caption = '', $class = 'IteratorExportExcel', $parms = array() )
 	{
 		return parent::getJSCall(
			array_merge(
				array(
					'caption' => $caption != '' ? $caption : $this->getCaption(),
					'class' => $class
				),
				$parms
			)
		);
 	}
 	
 	function execute_request()
 	{
 		parent::execute_request();
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
