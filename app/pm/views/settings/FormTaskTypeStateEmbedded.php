<?php
include "FieldTaskTypeStateDictionary.php";

class FormTaskTypeStateEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'State':
 				return true;
 			default:
 				return false;
 		}
 	}

    function IsAttributeObject( $attribute )
    {
        switch ( $attribute )
        {
            case 'State':
                return true;
            default:
                return parent::IsAttributeObject( $attribute );
        }
    }

    function createField( $attr )
    {
        switch ( $attr )
        {
            case 'State':
                return new FieldTaskTypeStateDictionary(getFactory()->getObject('IssueState'));
            default:
                return parent::createField( $attr );
        }
    }
}
