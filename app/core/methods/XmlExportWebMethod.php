<?php
include_once "ExportWebMethod.php";

class XmlExportWebMethod extends ExportWebMethod
{
 	function getCaption() {
 		return 'XML';
 	}
 	
 	function url( $class = 'IteratorExportXml' ) {
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
}
