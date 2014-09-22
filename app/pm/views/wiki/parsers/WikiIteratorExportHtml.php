<?php

include_once "WikiConverterTemplate.php";

class WikiIteratorExportHtml extends IteratorExport
{
	function export()
	{
		$converter = new WikiConverterTemplate ( $_REQUEST['template'] );
		
	 	$converter->setObjectIt( $this->getIterator() );

		$converter->parse();
	}
}
