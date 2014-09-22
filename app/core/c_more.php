<?php
 
 include_once SERVER_ROOT_PATH.'ext/html/html2text.php';
 include_once SERVER_ROOT_PATH.'core/methods/GetWholeTextWebMethod.php';

 function drawMore( $object_it, $attr_name, $max_words = 20, $addition = '' )
 {
 	if ( strtolower(get_class($object_it->object)) == 'metaobject' )
 	{
 		$object_class = $object_it->object->getClassName();
 	}
 	else
 	{
 		$object_class = get_class($object_it->object);
 	}
 	
 	$object_id = $object_it->getId();
 	$object_uid = md5($object_class.$object_id.$attr_name.rand(1, 1000)); 
	$max_width = 300;
	$converter = '';
 
	if( $max_words > 0 )
	{
		$attr_value = trim($object_it->getHtmlDecoded($attr_name));
		
        if ( $object_it->object->getAttributeType($attr_name) == 'wysiwyg' && !$object_it->object->IsAttributeStored($attr_name) )
        {
            // special case for wysiwyg attribute
            
            $attr_value = str_replace(chr(10), '', $attr_value);
        }		

	    if ( $object_it->object->getAttributeType($attr_name) == 'wysiwyg' )
        {
            // special case for wysiwyg attribute
            
            $attr_value = str_replace(chr(10), '', $attr_value);
        }		
        
        $entity_name = $object_it->object->getClassName();
		
		$totext = new html2text( addslashes($attr_value) );

		$attr_value = $totext->get_text();
		
		$converter = 'html2text';

		$result_value = str_replace( '...', '', $object_it->getWordsOnlyValue($attr_value, $max_words) );
	}
	else
	{
		$attr_value = " ";
		$result_value = $attr_value;
	}

	if( trim($result_value, '.') != trim($attr_value, '.') ) 
	{
		$method = new GetWholeTextWebMethod($object_it, $attr_name);
		
		$method->setRedirectUrl("function( value ) { $('#".$object_uid."Text').html(value); }");
		
		$hint_method = $method->getJSCall();
		
		echo '<span id="'.$object_uid.'" ondblclick="'.$hint_method.'">';
			echo '<span id="'.$object_uid.'Anchor" style="display:none;">';
			echo '</span>';

			echo $addition;
			
			echo '<span id="'.$object_uid.'Text" style="">';
				echo $object_it->getHtmlValue($result_value);
				
				echo '&nbsp; <a onclick="'.$hint_method.'" class="more" title="'.text(1321).'">&raquo;&raquo;&raquo;</a>';
			echo '</span>';
		echo '</span>';  	
	} 
	else 
	{
		echo $object_it->getHtmlValue($attr_value);
	}
 }
