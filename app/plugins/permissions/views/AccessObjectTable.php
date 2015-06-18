<?php

include "AccessObjectList.php";

class AccessObjectTable extends PMPageTable
{
    var $object_it;

    function __construct()
    {
    	parent::__construct( $this->buildObject() );
    }	
    
    function buildObject()
    {
        global $model_factory, $_REQUEST;

        $object = $model_factory->getClass($_REQUEST['class']) != ''
            ? $model_factory->getObject($_REQUEST['class']) : $model_factory->getObject('pm_ObjectAccess');
        
        $this->object_it = $_REQUEST['id'] > 0 
            ? $object->getExact($_REQUEST['id']) : $object->getEmptyIterator();
        
        return $model_factory->getObject('pm_ObjectAccess');
    }

    function getList()
    {
        return new AccessObjectList( $this->object, $this->object_it );
    }

    function getCaption()
    {
        $uid = new ObjectUID;

        return text(692).': '.$uid->getUidWithCaption($this->object_it);
    }

    function getFilterActions()
    {
        return array();
    }
    
    function getNewActions()
    {
        return array();
    }
}