<?php
include_once SERVER_ROOT_PATH."core/classes/export/WikiIteratorExport.php";
include 'WikiConverterMPdf.php';

class WikiIteratorExportPdf extends WikiIteratorExport
{
	function export()
	{
        $converter = new WikiConverterMPdf();
 		$iterator = $this->getIterator();

        $this->getIterator()->moveFirst();
        $documents = array_unique($this->getIterator()->fieldToArray('DocumentId'));
        $converter->setTitle( count($documents) < 2
            ? $this->getIterator()->getDisplayName()
            : $this->getName() );

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
