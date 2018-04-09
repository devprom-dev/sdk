<?php
include_once SERVER_ROOT_PATH."core/classes/export/WikiIteratorExport.php";
include_once "WikiConverterPreview.php";

class WikiIteratorExportHtml extends WikiIteratorExport
{
	function export()
	{
		$converter = new WikiConverterPreview();
        $converter->setOptions($this->getOptions());
        $converter->setObjectIt( $this->getIterator() );
		$converter->parse();
	}
}
