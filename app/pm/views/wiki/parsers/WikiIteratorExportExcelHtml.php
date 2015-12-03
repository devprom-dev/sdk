<?php

include_once SERVER_ROOT_PATH."core/classes/export/IteratorExportExcel.php";
include_once SERVER_ROOT_PATH.'core/classes/html/HtmlImageConverter.php';

class WikiIteratorExportExcelHtml extends IteratorExportExcel
{
 	function setFields( $fields )
 	{
 	    $object = $this->getIterator()->object;
 	    
 		parent::setFields( array_merge( $fields, array(
 		        'Content' => translate($object->getAttributeUserName('Content')),
 				'Caption' => translate($object->getAttributeUserName('Caption')),
 				'ParentPage' => translate($object->getAttributeUserName('ParentPage')),
 		        'ContentEditor' => translate($object->getAttributeUserName('ContentEditor'))
 	    ))); 
 	}
 	
 	function getValue( $key, $iterator )
 	{
 		if ( $key != 'Content' ) return parent::getValue( $key, $iterator );

		$content = $iterator->getHtmlDecoded($key);
		$content = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceImageCallback'), $content);
		return array('<![CDATA['.$content.']]>', 'String');
 	}
}
