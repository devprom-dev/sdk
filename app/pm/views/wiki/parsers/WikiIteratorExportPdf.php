<?php

class WikiIteratorExportPdf extends IteratorExport
{
	function export()
	{
 		global $_REQUEST, $model_factory;

		if ( function_exists('mb_strlen') )
		{
 			include 'WikiConverterMPdf.php';
 			
			$converter = new WikiConverterMPdf();
			
 			$converter->setTitle( $this->getName() );
		}
		else
		{
			include 'WikiConverterPDF.php';
			
		 	$converter = new WikiConverterPdf();
		}
 		 		
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
