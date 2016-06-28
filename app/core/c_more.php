<?php

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
	$attribute_type = $object_it->object->getAttributeType($attr_name);

	if( $max_words > 0 )
	{
	    if ( $attribute_type == 'wysiwyg' ) {
            // special case for wysiwyg attribute
            $attr_value = preg_replace('/^<p>(.+)<\/p>$/i', "\\1", str_replace(chr(10), '', $object_it->getHtmlDecoded($attr_name)));
        }
        else {		
            $attr_value = str_replace(chr(10), '', $object_it->get($attr_name));
        }

        $entity_name = $object_it->object->getClassName();
		
		$totext = new \Html2Text\Html2Text( addslashes($attr_value) );
		$attr_value = $totext->getText();

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
		if ( $attribute_type == 'wysiwyg' ) {
			$method->setRedirectUrl("function( value ) { $('#".$object_uid."Text').html(value); }");
		}
		else {
			$method->setRedirectUrl("function( value ) { $('#".$object_uid."Text').text(value); }");
		}
		
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
