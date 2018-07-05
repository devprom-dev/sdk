<?php

include "TaskTypeIterator.php";
include "predicates/TaskTypeBaseIterationRelatedPredicate.php";
include "predicates/TaskTypeFixBugPredicate.php";
include "predicates/TaskTypeNonBugFixPredicate.php";
include "predicates/TaskTypeBaseCategoryPredicate.php";
include "predicates/TaskTypeStateRelatedPredicate.php";

class TaskType extends MetaobjectCacheable 
{
 	function __construct() 
 	{
		parent::__construct('pm_TaskType');
		$this->setSortDefault(new SortAttributeClause('Caption'));
		$this->setAttributeDescription('ParentTaskType', text(1883));

		foreach( array('Description','OrderNum','ReferenceName') as $attribute ) {
			$this->addAttributeGroup($attribute, 'additional');
			$this->setAttributeRequired($attribute, false);
		}
 	}
	
	function createIterator() 
	{
		return new TaskTypeIterator($this);
	}
	
	function getPage()
	{
	    return getSession()->getApplicationUrl($this).'project/dicts/TaskType?';
	}

	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
			case 'ReferenceName':
				return uniqid('TaskType_');
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}

	function getForRole( $project_role_it )
	{
		return $this->getByRefArray(
			array ( 'ProjectRole' => $project_role_it->getId() )
			);
	}

	function add_parms( $parms )
	{
		global $model_factory;
		
		if ( $parms['ReferenceName'] == '' && $parms['ParentTaskType'] > 0 )
		{
			$base = $model_factory->getObject('TaskTypeBase');
			
			$base_it = $base->getExact($parms['ParentTaskType']);
	
			$parms['ReferenceName'] = $base_it->get('ReferenceName');
		}
		
		return parent::add_parms( $parms );
	}

	function modify_parms( $id, $parms )
	{
		global $model_factory;
		
		if ( $parms['ReferenceName'] == '' )
		{
			$base = $model_factory->getObject('TaskTypeBase');
			$base_it = $base->getExact($parms['ParentTaskType']);
	
			$parms['ReferenceName'] = $base_it->get('ReferenceName');
		}
		
		return parent::modify_parms( $id, $parms );
	}


}