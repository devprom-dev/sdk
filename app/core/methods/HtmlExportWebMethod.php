<?php

include_once "ExportWebMethod.php";

class HtmlExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return text(2510);
 	}
 	
 	function url( $class = 'IteratorExportHtml' )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
 	
}
