<?php

class TagForm extends PMPageForm
{
	function TagForm() 
	{
		global $model_factory;
		parent::PMPageForm( $model_factory->getObject('Tag') );
	}

 	function IsNeedButtonNew() {
		return false;
	}

 	function IsNeedButtonCopy() {
		return false;
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'Caption':
 			case 'OrderNum':
 				return true;
 				
 			default:
 				return false;
 		}
	}
}
