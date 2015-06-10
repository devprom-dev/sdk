<?php

include "AccessRightList.php";

class AccessRightTable extends PMPageTable
{
    var $role_it;

    function getAccessObject()
    {
        $access = getFactory()->getObject('pm_AccessRight');
        $access->addSort( new SortAttributeClause('RecordKey') );
        return $access;
    }

    function getList()
    {
        return new AccessRightList( $this->object );
    }

    function getFilters()
    {
        global $_REQUEST, $model_factory;
        	
        $role_filter = new FilterObjectMethod($model_factory->getObject('pm_ProjectRole'), translate('Роль'), 'role');
        
        $role_filter->setType( 'singlevalue' );
        
        $role_filter->setHasNone( false );

        $role_filter->setDefaultValue( array_shift(getSession()->getParticipantIt()->getRoles()) );
        
        $filters = array(
                $role_filter,
                new AccessObjectFilterViewWebMethod
        );
        	
        if ( $_REQUEST['object'] == 'object' )
        {
            array_push( $filters, new AccessClassFilterViewWebMethod() );
        }
        	
        if ( $_REQUEST['object'] == 'report' )
        {
            $method = new FilterAutoCompleteWebMethod( $model_factory->getObject('PMReport'), translate('Отчеты'));
            $method->setValueParm('report');

            array_push( $filters, $method );
        }
        	
        return $filters;
    }
    
    function getFilterPredicates()
    {
        $values = $this->getFilterValues();

        $predicates = array(
                new CommonAccessObjectPredicate( $values['object'] ),
                new CommonAccessRolePredicate( $values['role'] ),
                new CommonAccessClassPredicate( $values['class'] ),
                new CommonAccessReportPredicate( $values['report'] )
        );
        	
        return array_merge( parent::getFilterPredicates(), $predicates );
    }

    
    
    function _getFiltersDefault()
    {
        return array('role', 'object');
    }

    function getNewActions()
    {
        return array();
    }
    
    function getActions()
    {
        return array();
    }
    
    function getDeleteActions()
    {
    	return array();
    }
}