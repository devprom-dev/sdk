<?php

include_once SERVER_ROOT_PATH."pm/methods/c_date_methods.php";

include "ProjectLogList.php";

class ProjectLogTable extends PMPageTable
{
	private $object_it;
	
	function getList()
	{
		return new ProjectLogList( $this->getObject() );
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'ChangeDate.D';
		}
		
		if ( $sort_parm == 'sort2' )
		{
			return 'RecordModified.D';
		}
		
		return parent::getSortDefault( $sort_parm );
	}
	
	function buildObjectIt()
	{
		global $model_factory;
		
		if ( in_array($_REQUEST['object'], array('','all','none')) ) return $this->getObject()->getEmptyIterator();
		 
	    $classes = preg_split('/,/', $_REQUEST['object']);
		    
		if ( count($classes) != 1 ) return $this->getObject()->getEmptyIterator();
		
		$class_name = $model_factory->getClass($classes[0]);
		
		if ( !class_exists($class_name) ) return $this->getObject()->getEmptyIterator();
		
		$object = $model_factory->getObject($class_name);

        $object_id = $_REQUEST[strtolower(get_class($object))];
        
    	if ( $object_id < 1 )
    	{
			return $this->getObject()->getEmptyIterator();
    	} 
    		
        return $object->getExact($object_id);
	}
	
	function getObjectIt()
	{
		if ( is_object($this->object_it) ) return $this->object_it;

		$this->object_it = $this->buildObjectIt();
		
        return $this->object_it;
	}
	
	function getFilters()
	{
		global $_REQUEST, $model_factory;
		
		$filters = array(
			new ViewStartDateWebMethod(),
			new ViewFinishDateWebMethod(),
			new ViewLogSubjectWebMethod()
		);	
		
		
 		$filters[] = $this->buildEntityFilter();
 		$filters[] = $this->buildActionsFilter();
		
		if ( !in_array($_REQUEST['object'], array('','all','none')) )
		{
		    $classes = preg_split('/,/', $_REQUEST['object']);
		    
		    if ( count($classes) == 1 )
		    {
		    	$class_name = $model_factory->getClass($classes[0]);
		    	
		    	if ( class_exists($class_name) ) $filters[] = new FilterAutoCompleteWebMethod($model_factory->getObject($class_name));
		    }
		}
		
		return $filters;
	}
	
	function buildEntityFilter()
	{
		$entity_filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogEntitySet'), '', 'object' );
		$entity_filter->setHasNone( false );
		$entity_filter->setIdFieldName( 'ClassName' );
		
		return $entity_filter;
	}
	
	function buildActionsFilter()
	{
		$filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogAction'), '', 'action' );
		$filter->setHasNone( false );
		$filter->setIdFieldName( 'ReferenceName' );
		
		return $filter;
	}
	
	function getFilterValues()
	{
		$values = parent::getFilterValues();
		
		if ( $_REQUEST['start'] == '' )
		{
			$values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-1 weeks', strtotime(date('Y-m-j')))); 
		}
		
		return $values;
	}
	
	function getFilterPredicates()
	{
		global $_REQUEST, $model_factory;
		
		$values = $this->getFilterValues();
		
		$filters = array(
			new ChangeLogActionFilter( $values['action'] ),
			new ChangeLogParticipantFilter( $values['participant'] ),
			new ChangeLogStartFilter( $values['start'] ),
			new ChangeLogFinishFilter( $values['finish'] )
		);
		
		$object_it = $this->getObjectIt();
		
		if ( $object_it->getId() > 0 )
		{
			$filters[] = new ChangeLogItemFilter( $object_it );
		}
		else if ( !in_array($_REQUEST['object'], array('','all','none')) )
		{
		    $classes = preg_split('/,/', $_REQUEST['object']);
		    
	    	$class_name = $model_factory->getClass($classes[0]);
				
	    	if ( class_exists($class_name) ) $filters[] = new ChangeLogObjectFilter( $class_name );
		}
 		
		return $filters;
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
	
	function IsNeedToDelete() { return false; }
} 