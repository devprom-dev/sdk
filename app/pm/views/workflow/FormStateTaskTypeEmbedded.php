<?php
class FormStateTaskTypeEmbedded extends PMFormEmbedded
{
    function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'TaskType':
 				return true;
 			default:
 				return false;
 		}
 	}

    function getAttributeObject( $attr )
    {
        $object = parent::getAttributeObject( $attr );
        switch ( $attr ) {
            case 'TaskType':
                $object->addFilter( new FilterBaseVpdPredicate() );
                break;
        }
        return $object;
    }

    function createField( $attr )
    {
        switch ( $attr )
        {
            default:
                return parent::createField( $attr );
        }
    }
}
