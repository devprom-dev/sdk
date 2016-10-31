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

    function get( $key )
    {
        switch( $key ) {
            case 'ParentPage':
                return $this->getIterator()->getRef($key)->getHtmlDecoded('Caption');

            default:
                return parent::get( $key );
        }
    }

 	function getValue( $key, $iterator )
 	{
 	    switch( $key ) {
            case 'Content':
                $content = $iterator->getHtmlDecoded($key);
                $content = preg_replace_callback(
                    '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceImageCallback'),
                    $content
                );
                return array('<![CDATA['.$content.']]>', 'String');

            default:
                return parent::getValue( $key, $iterator );
        }
 	}
}
