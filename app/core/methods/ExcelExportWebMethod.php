<?php

include_once "ExportWebMethod.php";

class ExcelExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return translate('Ёкспорт в Excel');
 	}
 	
 	function getJSCall( $caption = '', $class = 'IteratorExportExcel' )
 	{
 		return parent::getJSCall( array( 
 		    'caption' => $caption != '' ? $caption : $this->getCaption(),
 		    'class' => $class
 		));
 	}
 	
 	function execute_request()
 	{
 		parent::execute_request();
 		
 		echo '&caption='.SanitizeUrl::parseUrl($_REQUEST['caption']);
 	}
}
