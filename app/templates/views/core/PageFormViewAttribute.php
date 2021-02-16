<?php

switch ( $class )
{
	case 'fieldnumber':
    case 'fieldvelocity':
    case 'fieldhours':
    case 'fieldhourspositivenegative':
	case 'fieldtext':
	case 'fieldlargetext':
	case 'fieldshorttext':
    case 'fieldcomputed':
        if ( $editable && $editmode && is_object($field) ) {
            $field->render( $view );
        }
        else {
            echo $text;
        }
		break;

    case 'fielddatetime':
    case 'fielddate':
        if ( $editable ) {
            echo '<a class="btn btn-xs btn-light" onclick="selectDate(event, \''.$url.'\', \''.$value.'\', $(this))">'.($text != '' ? $text : '...').'</a>';
        }
        else {
            echo $text;
        }
        break;

    case 'fieldcheck':
        echo '<span name="'.$field->getId().'" class="'.get_class($field).'">';
            if ( $editable && $field->readonly() ) {
                $field->showPopupMenu();
            }
            $field->render( $view );
        echo '</span>';
        break;

	default:
        if ( is_object($field) )
        {
            echo '<span name="'.$field->getId().'" class="'.get_class($field).'">';
                $field->render( $view );
            echo '</span>';
        }
        else
        {
            echo $html;
        }
		break;
}
