<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExportExcel.php";

class WikiIteratorExportExcelText extends IteratorExportExcel
{
 	function setFields( $fields )
 	{
 		if ( $this->getIterator()->count() < 1 )
 		{
 			parent::setFields( $fields ); return;
 		}
 		
 	    $object = $this->getIterator()->object;
 	    
 		parent::setFields( array_merge( $fields, array(
 				'ParentPage' => translate($object->getAttributeUserName('ParentPage')),
 				'Caption' => translate($object->getAttributeUserName('Caption')),
 				'Content' => translate($object->getAttributeUserName('Content')),
 				'ContentEditor' => translate($object->getAttributeUserName('ContentEditor'))
 	    ))); 
 	}
}
