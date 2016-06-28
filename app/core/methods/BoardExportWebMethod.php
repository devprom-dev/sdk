<?php

include_once "ExportWebMethod.php";

class BoardExportWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return translate('Печать карточек');
 	}

 	function url( $class )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
}
