<?php

class FormStateActionEmbedded extends PMFormEmbedded
{
 	var $entity;
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ReferenceName':
            case 'Parameters':
            case 'IsNotifyUser':
 				return true;
 			default:
 				return false;
 		}
 	}
 	
 	function setEntity( $entity ) {
 		$this->entity = $entity;
 	}
 	
	function createField( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'ReferenceName':
				return new FieldDictionary(
                    getFactory()->getObject('StateBusinessAction')->getRegistry()->Query(
                        array(
                            new StateBusinessEntityFilter($this->entity)
                        )
                    )
                );
			default:
				return parent::createField( $attr_name );
		}
	}
}