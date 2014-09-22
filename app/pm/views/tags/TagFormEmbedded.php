<?php

class TagFormEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Tag':
 				return true;
 				
 			default:
 				return false;
 		}
 	}
 	
 	function getActions( $object_it, $item )
 	{
 	    $actions = array();
 	    
 	    $actions[] = array (
 	        'name' => translate('Показать все'),
 	        'url' => $object_it->object->getPage()
 	    );
 	    $actions[] = array();
 	    
 	    return array_merge($actions, parent::getActions( $object_it, $item ));
 	}
 	
	function drawFieldTitle( $attr )
 	{
 	}
	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Tag':
				
 			    $object = $this->getAttributeObject( $attr );

				$field = new FieldAutoCompleteObject( $object );
				
				$field->setTitle( $object->getDisplayName() );

				$field->setAppendable(); 
				
				return $field;
				
 			default:
 			    
 				return parent::createField( $attr );
 		}
 	}
}