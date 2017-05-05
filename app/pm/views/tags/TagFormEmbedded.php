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
            'name' => text(2449),
            'url' => $object_it->getViewUrl()
        );
        $actions[] = array();
 	    $actions[] = array (
 	        'name' => text(2448),
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
                $field->setMultiple();
				return $field;
				
 			default:
 				return parent::createField( $attr );
 		}
 	}
}