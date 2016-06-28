<?php

include_once "ExportWebMethod.php";

class HtmlExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return translate('Печать списка');
 	}
 	
 	function url( $class = 'IteratorExportHtml' )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
 	
}
