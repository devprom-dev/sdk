<?php

class SubversionList extends PMPageList
{
    function IsNeedToDisplay( $attr )
    {
        $type = $this->object->getAttributeDbType( $attr );
        return $type == '' && $type != 'OrderNum';
    }

    function IsNeedToDisplayLinks( )
    {
        return false;
    }

    function IsNeedToDelete( )
    {
        return false;
    }

    function IsNeedToDisplayOperations()
    {
        return false;
    }

    function getGroupFields()
    {
        return array();
    }
}
