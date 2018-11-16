<?php

switch ( $class )
{
	case 'fieldnumber':
    case 'fieldhours':
    case 'fieldhourspositivenegative':
	case 'fieldtext':
	case 'fieldlargetext':
	case 'fieldshorttext':
	case 'fielddatetime':
    case 'fielddate':

		echo IteratorBase::getHtmlValue($text);
		
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
                echo '<span name="'.$field->getId().'">';
		            $field->render( $view );
                echo '</span>';
		    }
		    else
		    {
		        echo $html;
		    }
		}
		
		break;
}
