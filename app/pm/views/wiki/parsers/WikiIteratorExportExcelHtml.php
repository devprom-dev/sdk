<?php

include_once SERVER_ROOT_PATH."core/classes/export/IteratorExportExcel.php";

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
 		if ( $key == 'Content' )
 		{
 			$content = $iterator->getHtmlDecoded($key);
 			
 			$content = preg_replace_callback( '/<img\s+([^>]*)>/i', array($this, 'replaceImageCallback'), $content);
 			
 			return array('<![CDATA['.$content.']]>', 'String');
 		}
 		
 		return parent::getValue( $key, $iterator );
 	}
 	
    function replaceImageCallback( $match )
    {
     	$attributes = $match[1];
     	
     	if ( preg_match( '/src="([^"]+)"/i', $attributes, $attrs ) ) $url = $attrs[1];
     	
     	if ( $url == '' ) return $match[0];
     	
     	if ( !preg_match('/file\/([^\/]+)\/([^\/]+)\/([\d]+).*/i', $url, $result) ) return $match[0];
     	
		$file_class = $result[1];
		$file_project = $result[2];
		$file_id = $result[3];
 	        
 	    $file_class = getFactory()->getClass($file_class);
 	    
 	    if ( !class_exists($file_class) ) return $match[0];
 	    
		$file_it = getFactory()->getObject($file_class)->getRegistry()->Query(
				array(
						new FilterInPredicate($file_id)
				)
		);

		if ( $file_it->getId() < 1 ) return $match[0];
		
     	$image = file_get_contents($file_it->getFilePath('Content'));
     	
     	if ( $image === false ) return $match[0];
     	
     	$src = 'data:image;base64,'.base64_encode($image);
     	
     	$match[0] = preg_replace('/src="[^"]+"/i', 'src="'.$src.'"', $match[0]);
     	
     	return $match[0];
    } 
}
