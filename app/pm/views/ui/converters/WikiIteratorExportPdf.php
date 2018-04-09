<?php
include_once SERVER_ROOT_PATH."core/classes/export/WikiIteratorExport.php";
include 'WikiConverterMPdf.php';

class WikiIteratorExportPdf extends WikiIteratorExport
{
	function export()
	{
        $converter = new WikiConverterMPdf();
    	$converter->setTitle( $this->getName() );

 		$iterator = $this->getIterator();

 		if ( $iterator->object->getClassName() == 'WikiPageChange' ) {
		 	$converter->setRevision( $iterator );
		}
		else {
		 	$converter->setObjectIt( $iterator );
		}

        $converter->setOptions($this->getOptions());
        $converter->parse();
	}
}
