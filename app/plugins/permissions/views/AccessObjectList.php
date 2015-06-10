<?php

class AccessObjectList extends PMPageList
{
    var $object_it;

    function __construct( $object, $object_it )
    {
        $this->object_it = $object_it;

    	$registry = new AccessObjectRegistry($object);
    	
    	$registry->setObjectIt($this->object_it);
        
    	$object->setRegistry($registry);
    	
        parent::__construct( $object );
    }

    function IsNeedToDisplay( $attr )
    {
        switch( $attr )
        {
            case 'ObjectClass':
            case 'ObjectId':
                return false;
                	
            default:
                return parent::IsNeedToDisplay( $attr );
        }
    }

    function drawCell( $object_it, $attr )
    {
        switch ( $attr )
        {
            case 'AccessType':

                $method = new ObjectAccessWebMethod;
                
                if ( $this->object_it->getId() > 0 && $method->hasAccess() )
                {
                    $method->drawSelect($object_it, $this->object_it);
                }
                
                break;

            default:
                return parent::drawCell( $object_it, $attr );
        }
    }

    function getColumnWidth( $attr )
    {
        if ( $attr == 'AccessType' )
        {
            return '200';
        }
        else
        {
            return parent::getColumnWidth( $attr );
        }
    }

    function IsNeedToDelete( )
    {
        return false;
    }

    function IsNeedToDisplayNumber()
    {
        return false;
    }

    function getItemActions( $column, $object_it )
    {
        return array();
    }
    
    function getGroupFields()
    {
        return array();
    }
}