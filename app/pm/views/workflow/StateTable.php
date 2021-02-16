<?php
include 'StateList.php';

class StateTable extends SettingsTableBase
{
    function getList()
    {
        return new StateList( $this->getObject() );
    }

    function getCaption()
    {
        return $this->getObject()->getDisplayName();
    }

	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' ) return 'OrderNum';
	    
		return parent::getSortDefault( $sort_parm );
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
	
    function getFilters()
    {
        return parent::getFilters();
    }
    
 	function getFilterPredicates( $values )
 	{
		return array_merge( 
				parent::getFilterPredicates( $values ),
				array (
						new FilterBaseVpdPredicate()
				)
		);
 	}

 	function getActions()
    {
        $actions = parent::getActions();
        $module_it = getFactory()->getObject('Module')->getExact('process/storesettings');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $actions[] = array();
            $actions[] = array (
                'name' => $module_it->getDisplayName(),
                'url' => $module_it->getUrl()
            );
        }
        return $actions;
    }

    function getDetails() {
        return array();
    }
}
