<?php

include "RoleUsageList.php";

class RoleUsageTable extends ResourceTable
{
    var $role_it;

    function __construct( $object )
    {
        if ( $_REQUEST['viewpoint'] == '' )
        {
            $_REQUEST['viewpoint'] = 'users';
        }
        	
        $role = getFactory()->getObject('pm_ProjectRole');
        
        $this->role_it = $_REQUEST['role'] > 0 
        		? $role->getRegistry()->Query(array(new FilterInPredicate($_REQUEST['role'])))
        		: $role->getEmptyIterator();

        parent::__construct( $object );
    }

    function getList()
    {
        return new RoleUsageList( $this->getObject(), $this->role_it );
    }

    function getFilters()
    {
        global $model_factory;
        
        $filters = array(
                new FilterObjectMethod($model_factory->getObject('ProjectRoleBase'), '', 'role'),
        );

        return array_merge( $filters, parent::getFilters() );
    }
}