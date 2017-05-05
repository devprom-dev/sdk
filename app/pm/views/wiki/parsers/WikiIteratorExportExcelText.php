<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExportExcel.php";

class WikiIteratorExportExcelText extends IteratorExportExcel
{
 	function setFields( $fields )
 	{
 		if ( $this->getIterator()->count() < 1 ) {
 			parent::setFields( $fields ); return;
 		}
 		
 	    $object = $this->getIterator()->object;
        if ( !array_key_exists('SectionNumber', $fields) ) {
            $fields = array_merge($fields, array(
                'ParentPage' => translate($object->getAttributeUserName('ParentPage'))
            ));
        }
 	    
 		parent::setFields( array_merge( $fields, array(
            'Caption' => translate($object->getAttributeUserName('Caption'))
 	    ))); 
 	}
}
