<?php

include_once "ExportWebMethod.php";

class BoardExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return translate('������ ��������');
 	}
 	
 	function getJSCall( $class )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
}
