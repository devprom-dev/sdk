<?php

include "TerminologyList.php";
include "TerminologyTableSettingsBuilder.php";

class TerminologyTable extends PMPageTable
{
    function __construct()
    {
        parent::PMPageTable( $this->getObject() );
        
        getSession()->addBuilder( new TerminologyTableSettingsBuilder() ); 
    }
    
 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('CustomResource');
 	}
 	
	function getList()
	{
		return new TerminologyList( $this->getObject() );
	}

	function getFiltersDefault()
	{
	    return array('searchsystem', 'overriden');
	}
	
	function getSortFields()
	{
		return array();
	}

	function getFilters()
	{
		return array (
			new FilterTextWebMethod( text(941), 'searchsystem' ),
			new ViewTerminologyWebMethod()
		);
	}

	function getNewActions()
	{
		return array();
	}
	
	function getActions()
	{
		$actions = array();
		
		$method = new DeleteCustomTerminologyWebMethod();
		if ( $method->hasAccess() )
		{
			array_push( $actions, array( 'url' => $method->getJSCall(), 'name' => $method->getCaption() ) );
		}
		
		return $actions;
	}
	
	function getDeleteActions()
	{
		return array();
	}
}
