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

        header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-control: no-store");
        header('Content-Type: text/html; charset='.APP_ENCODING);
        header(EnvironmentSettings::getDownloadHeader($this->getName().'.html'));

        $converter->parse();
	}
}
