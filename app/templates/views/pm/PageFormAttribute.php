<?php

switch ( $class )
{
	case 'fieldnumber':
    case 'fieldhours':
	case 'fieldtext':
	case 'fieldlargetext':
	case 'fieldshorttext':
	case 'fielddatetime':
    case 'fielddate':

		echo $text;
		
		break;
		
	default:
		$b_text_only = is_a($field, 'fieldautocompleteobject')
            || is_a($field, 'FieldListOfReferences')
			|| is_a($field, 'fielddictionary');
			
		if ( $b_text_only )
		{
			echo $text;
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
