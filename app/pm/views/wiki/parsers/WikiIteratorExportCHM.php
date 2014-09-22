<?php

include_once "WikiConverterCHM.php";

class WikiIteratorExportCHM extends IteratorExport
{
	function export()
	{
		$converter = new WikiConverterChm ( $_REQUEST['template'] );
		
	 	$converter->setObjectIt( $this->getIterator() );

		$converter->parse();
	}
}
