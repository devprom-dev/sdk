<?php

switch ( $class )
{
	case 'fieldnumber':
	case 'fieldtext':
	case 'fieldshorttext':
	case 'fielddatetime':
		
		echo $text;
		
		break;
		
	default:
		$b_text_only = is_a($field, 'fieldautocompleteobject') 
			|| is_a($field, 'fielddictionary');
			
		if ( $b_text_only )
		{
			echo IteratorBase::getHtmlValue($text);
		}
		else
		{
		    if ( is_object($field) )
		    {
		        $field->render( $view );
		    }
		    else
		    {
		        echo $html;
		    }
		}
		
		break;
}

?>