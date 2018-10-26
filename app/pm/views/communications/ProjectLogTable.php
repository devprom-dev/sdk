<?php

include_once SERVER_ROOT_PATH."pm/methods/c_date_methods.php";
include_once SERVER_ROOT_PATH . "pm/classes/communications/predicates/ChangeLogStateFilter.php";

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
			return 'RecordModified.D';
		}
		
		return parent::getSortDefault( $sort_parm );
	}

	function getSortFields()
    {
        return array('RecordModified', 'SystemUser', 'Project', 'SystemUser');
    }

    function buildObjectIt()
	{
        $values = $this->getFilterValues();
        if ( $values['entities'] == '' ) $values['entities'] = $_REQUEST['entities'];

		if ( in_array($values['entities'], array('','all','none')) ) return $this->getObject()->getEmptyIterator();
		 
	    $classes = preg_split('/,/', $values['entities']);
		if ( count($classes) != 1 ) return $this->getObject()->getEmptyIterator();
		
		$class_name = getFactory()->getClass($classes[0]);
		if ( !class_exists($class_name) ) return $this->getObject()->getEmptyIterator();
		
		$object = getFactory()->getObject($class_name);
        $object_id = $_REQUEST[strtolower(get_class($object))];
        
    	if ( $object_id < 1 ) {
			return $this->getObject()->getEmptyIterator();
    	} 
    		
        return $object->getExact($object_id);
	}
	
	function getObjectIt()
	{
		if ( is_object($this->object_it) ) return $this->object_it->copyAll();
		return $this->object_it = $this->buildObjectIt();
	}
	
	function getFilters()
	{
		$filters = array(
			$this->buildStartFilter(),
			new ViewFinishDateWebMethod(),
			new ViewLogSubjectWebMethod()
		);	
		
		
 		$filters[] = $this->buildEntityFilter();
 		$filters[] = $this->buildActionsFilter();
		
		if ( !in_array($_REQUEST['entities'], array('','all','none')) ) {
		    $classes = preg_split('/,/', $_REQUEST['entities']);
		    
		    if ( count($classes) == 1 ) {
		    	$class_name = getFactory()->getClass($classes[0]);
		    	if ( class_exists($class_name) ) $filters[] = new FilterAutoCompleteWebMethod(getFactory()->getObject($class_name));
		    }
		}

		$filters[] = $this->buildStateFilter();
		
		return array_merge(
		    parent::getFilters(),
            $filters
        );
	}
	
	function buildStartFilter()
	{
		if( array_key_exists('start',$_REQUEST) and in_array($_REQUEST['start'],array('','all','hide')) ) {
			unset($_REQUEST['start']);
		}
		$filter = new ViewStartDateWebMethod();
		return $filter;
	}
	
	function buildEntityFilter()
	{
		$entity_filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogEntitySet'), '', 'entities' );
		$entity_filter->setHasNone( false );
		$entity_filter->setIdFieldName( 'ClassName' );
        $entity_filter->setType('singlevalue');
		return $entity_filter;
	}
	
	function buildActionsFilter()
	{
		$filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogAction'), '', 'action' );
		$filter->setHasNone( false );
		$filter->setIdFieldName( 'ReferenceName' );
		
		return $filter;
	}

    function buildStateFilter()
    {
        $filter = new FilterObjectMethod( getFactory()->getObject('StateCommon'), translate('Состояние'), 'state' );
        $filter->setHasNone(false);
        return $filter;
    }

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();

		$filters = array(
			new ChangeLogActionFilter( $values['action'] ),
			new ChangeLogParticipantFilter( $values['participant'] ),
			new ChangeLogStartFilter( $_REQUEST['start'] ),
			new ChangeLogFinishFilter( $values['finish'] ),
			new ChangeLogVisibilityFilter(),
            new ChangeLogStateFilter( $values['state'] )
		);
		
		$object_it = $this->getObjectIt();
		if ( $object_it->getId() > 0 )
		{
			if ( $object_it->count() == 1 && $object_it->object instanceof WikiPage ) {
				$object_it = $object_it->object->getRegistry()->Query(
					array(
						new ParentTransitiveFilter($object_it->getId())
					)
				);
			}
			$filters[] = new ChangeLogItemFilter($object_it);
		}
		else if ( !in_array($values['entities'], array('','all','none')) )
		{
		    $classes = preg_split('/,/', $values['entities']);
	    	$class_name = getFactory()->getClass($classes[0]);
	    	if ( class_exists($class_name) ) $filters[] = new ChangeLogObjectFilter( $class_name );
		}
 		
		return array_merge(
		    parent::getFilterPredicates(),
            $filters
        );
	}
	
	function getNewActions()
	{
		$actions = array();
		$values = $this->getFilterValues();
		
		if ( $values['action'] == 'commented' )
		{
			$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Question'));
			if ( $method->hasAccess() )
			{
				$method->setRedirectUrl('donothing');
				$uid = strtolower('new-question');
				$actions[$uid] = array (
                    'uid' => $uid,
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(
                                array(
                                    'area' => $this->getPage()->getArea()
                                )
                             )
				);
			}
		}
		
		return $actions;
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

    protected function getFamilyModules( $module )
    {
        return array('whatsnew');
    }
}