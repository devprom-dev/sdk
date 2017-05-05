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

 	function getItemDisplayName($object_it) {
        return $object_it->getBackwardDisplayName();
    }

    function getFieldValue2( $attr )
    {
        if ( !is_object($this->getObjectIt()) ) return parent::getFieldValue($attr);
        switch( $attr ) {
            case $this->getAnchorField():
                return $this->getObjectIt()->get($this->getAnchorField());
            default:
                return parent::getFieldValue($attr);
        }
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
