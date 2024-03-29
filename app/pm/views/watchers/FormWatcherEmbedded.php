<?php

class FormWatcherEmbedded extends PMFormEmbedded
{
  	var $anchor_it;
 	
 	function setAnchorIt( $object_it )
 	{
 	    $this->anchor_it = $object_it;
 	}

 	function getFieldValue( $attr )
 	{
 	    switch( $attr )
 	    {
 	        case 'ObjectId':
 	            return $this->anchor_it->getId();
 	        
 	        case 'ObjectClass':
 	            return strtolower(get_class($this->anchor_it->object));
 	             
 	        default:
 	            return parent::getFieldValue( $attr );
 	    }
 	}
     
    function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'SystemUser':
 				return true;

 			case 'Email':
 				return false;
 		}
		return false;
 	}
 	
 	function drawFieldTitle( $attr )
 	{
 	}
 	
 	function createField( $attr )
 	{
 		$object = $this->getObject();
 		
 		switch ( $attr )
 		{
 			case 'SystemUser':
 				$field = new FieldAutoCompleteObject( getFactory()->getObject('IssueAuthorActive') );
 				$field->setTitle( translate($object->getAttributeUserName($attr)) );
				$field->setAppendable();
 				return $field;
 					
 			default:
 				return parent::createField( $attr );
 		}
 	}
}