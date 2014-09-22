<?php

include_once SERVER_ROOT_PATH."pm/classes/participants/predicates/UserParticipanceProjectPredicate.php";

class SubversionUserFormEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 		    case 'OrderNum':
 		    	return false;
 		    	
 			default:
 				return parent::IsAttributeVisible( $attribute );
 		}
 	}

	function getFieldDescription( $attr )
	{
		switch( $attr )
		{
		    case 'UserName':
		    	return text('sourcecontrol43');

		    case 'UserPassword':
		    	return text('sourcecontrol44');
		    	
		    default:
		    	return parent::getFieldDescription( $attr );
		}
	}
 	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'SystemUser':
 			    $object = $this->getAttributeObject( $attr );
 			    
 			    $object->addFilter( new UserParticipanceProjectPredicate(getSession()->getProjectIt()->getId()) );

 			    return new FieldDictionary( $object );
				
 			default:
 			    return parent::createField( $attr );
 		}
 	}
}