<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExport.php";
include_once "WikiConverterPreview.php";

class WikiIteratorExportHtml extends IteratorExport
{
	function export()
	{
		$converter = new WikiConverterPreview();
        $converter->setOptions($this->getOptions());
        $converter->setObjectIt( $this->getIterator() );
		$converter->parse();
	}
}
