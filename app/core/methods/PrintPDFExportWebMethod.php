<?php
include_once "ExportWebMethod.php";

class PrintPDFExportWebMethod extends ExportWebMethod
{
 	function getCaption() {
 		return text(2839);
 	}
 	
 	function url( $class = 'IteratorExportPDF' )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
}
