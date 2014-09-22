<?php

include 'StateList.php';

class StateTable extends PMPageTable
{
    var $object;

    function StateTable ( $object )
    {
        $this->object = $object;
        
        parent::PMPageTable( $this->object );
    }

    function getObject()
    {
        return $this->object;
    }

    function getList()
    {
        return new StateList( $this->getObject() );
    }

    function getCaption()
    {
        return $this->object->getDisplayName();
    }

	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' ) return 'OrderNum';
	    
		return parent::getSortDefault( $sort );
	}

 	function getSortFields()
	{
		$fields = parent::getSortFields();
		
		if ( !$this->getObject()->IsAttributeVisible('QueueLength') )
		{
			unset($fields[array_search('QueueLength', $fields)]);
		}
		
		return $fields;
	}
	
    function getUrl()
    {
        $session = getSession();
        
        return $session->getApplicationUrl().'project/workflow?dict='.SanitizeUrl::parseUrl($_REQUEST['dict']);
    }

    function getFilters()
    {
        return parent::getFilters();
    }
    
 	function getFilterPredicates()
 	{
		return array_merge( 
				parent::getFilterPredicates(),
				array (
						new FilterBaseVpdPredicate()
				)
		);
 	}
}
