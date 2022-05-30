<?php
$script = "javascript:processBulk('".$name."','"
    .$viewurl."&formonly=true&operation=Attribute".$referenceName."','".$objectid."', devpromOpts.updateUI);";

switch ( $class )
{
	case 'fieldnumber':
    case 'fieldvelocity':
    case 'fieldhours':
    case 'fieldhourspositivenegative':
        if ( $editable && is_object($field) ) {
            if ( $editmode ) {
                $field->render( $view );
            }
            else {
                echo '<a class="btn btn-xs btn-light" onclick="'.$script.'">'.($text != '' ? $text : '...').'</a>';
            }
        }
        else {
            echo $text;
        }
		break;

    case 'fieldtext':
    case 'fieldlargetext':
    case 'fieldshorttext':
        if ( $editable && is_object($field) ) {
            if ( $editmode ) {
                $field->render( $view );
            }
            else {
                echo $text . ' <a class="btn btn-xs btn-light" onclick="'.$script.'">...</a>';
            }
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

    case 'fieldcomputed':
        if ( $editable ) {
            $field->render( $view );
        }
        else {
            echo $text;
        }
        break;

	default:
        if ( is_object($field) )
        {
            echo '<span name="'.$field->getId().'" class="'.get_class($field).'">';
                if ( $editable && !$editmode && ($field instanceof FieldAutoCompleteObject || $field instanceof FieldDictionary) ) {
                    if ( html_entity_decode($text) != $text ) {
                        echo $text . ' <a class="btn btn-xs btn-light" onclick="'.$script.'">...</a>';
                    }
                    else {
                        echo '<a class="btn btn-xs btn-light" onclick="'.$script.'">'.($text != '' ? $text : '...').'</a>';
                    }
                }
                else {
                    $field->render( $view );
                }
            echo '</span>';
        }
        else
        {
            echo $html;
        }
		break;
}
