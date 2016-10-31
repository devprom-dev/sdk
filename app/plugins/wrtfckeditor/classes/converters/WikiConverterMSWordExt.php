<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExport.php";

class WikiConverterMSWordExt extends IteratorExport
{
	function export()
	{
        $item = getFactory()->getObject('Module')->getExact('wrtfckeditor/exportmsword')->buildMenuItem();
        exit(header('Location: '.$item['url']));
	}
}
