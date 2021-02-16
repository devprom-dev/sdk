<?php

class TagForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name )
 	{
 		switch ( $attr_name )
 		{
 			case 'Caption':
 			case 'OrderNum':
 				return true;

            case 'Project':
                return parent::IsAttributeVisible($attr_name);

            default:
 				return false;
 		}
	}
}
