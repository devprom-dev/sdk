<?php

class WikiIteratorExportRtf extends IteratorExport
{
	function export()
	{
 		global $_REQUEST, $model_factory;

 		include 'WikiConverterRtf.php';
 		
		$converter = new WikiConverterRtf();
		
		$converter->setTitle( $this->getName() );
		
 		$iterator = $this->getIterator();
 		
		if ( $iterator->object->getClassName() == 'WikiPageChange' )
		{
		 	$converter->setRevision( $iterator );
		}
		else
		{
		 	$converter->setObjectIt( $iterator );
		}
		 
	 	$converter->parse();
	}
}
