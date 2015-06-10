<?php

include "UserAccessRightList.php";

class UserAccessRightTable extends AccessRightTable
{
    var $user_it;
    
    var $participant_it;

    public function __construct( $object )
    {
    	global $model_factory;
    	
        $this->user_it = $model_factory->getObject('User')->getExact($_REQUEST['user']);
    	
        parent::__construct( $object );
    }
    
    function getAccessObject()
    {
        global $model_factory;
        	
        $access = parent::getAccessObject();
        	
        $access->addFilter( new AccessRightUserPredicate($this->user_it->getId()) );
        	
        return $access;
    }

    function getList()
    {
        $this->participant_it = getFactory()->getObject('Participant')->getRegistry()->Query(
        		array (
        				new FilterAttributePredicate('SystemUser', $this->user_it->getId()),
        				new FilterBaseVpdPredicate()
        		)
        );
        
        return new UserAccessRightList( $this->getObject(), $this->participant_it );
    }

    function getCaption()
    {
        return translate('Права доступа для пользователя').': '.$this->user_it->getDisplayName();
    }

    function getSortDefault( $sort_parm )
    {
        if ( $sort_parm == 'sort' )
        {
            return 'ReferenceType';
        }

        return parent::getSortDefault( $sort_parm );
    }
    
    function getFilters()
    {
        global $_REQUEST, $model_factory;
        	
        $filters = array(
                new AccessObjectFilterViewWebMethod
        );
        	
        if ( $_REQUEST['object'] == 'object' )
        {
            array_push( $filters,
            new AccessClassFilterViewWebMethod() );
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
        return array_merge( parent::getFilterPredicates(), 
        		array( 
        				new CommonAccessRolePredicate( array_shift(getFactory()->getObject('ProjectRole')->getAll()->idsToArray())) 
        			) 
        );
    }
}