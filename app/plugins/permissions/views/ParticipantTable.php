<?php

include "ParticipantList.php";

class ParticipantTable extends PMPageTable
{
	function getList()
	{
		return new ParticipantList( $this->getObject() );
	}

	function getFilters()
	{
		global $model_factory, $_REQUEST;

		$project = $model_factory->getObject('Project');
		
		$ids = getSession()->getProjectIt()->getRef('LinkedProject')->idsToArray();
		
		if ( !getSession()->getProjectIt()->IsPortfolio() )
		{
		    $ids[] = getSession()->getProjectIt()->getId();
		}
		
		$project->addFilter( new FilterInPredicate($ids) );

		$project_filter = new FilterObjectMethod( $project, translate('Проект'), 'participates-project' );
		
		$project_filter->setUseUid(false);
		
		$project_filter->setHasNone(false);
		
		$type_filter = new FilterObjectMethod(new UserParticipanceType(), translate('Пользователи'), 'type');
		
		$type_filter->setHasNone(false);
		
		$type_filter->setIdFieldName('ReferenceName');
		
		$type_filter->setDefaultValue('participant');
		
		$role = $model_factory->getObject('ProjectRole');
		
		$role->addFilter( new ProjectRoleInheritedFilter() );
		
		return array_merge( parent::getFilters(), array(
		    $type_filter,
			new FilterObjectMethod($role, '', 'role'),
		    $project_filter,
			new ViewParticipantCapacityWebMethod()
		));	
	}
	
	function getFilterPredicates()
	{
	    $values = $this->getFilterValues();
	    
		$predicates = array(
		    new UserParticipanceTypePredicate( $values['type'] ),
		    new UserParticipanceProjectPredicate( $values['participates-project'] ),
			new UserParticipanceRolePredicate( $values['role'] ),
			new UserParticipanceWorkloadPredicate( $values['workload'] )
		);		

		return array_merge(parent::getFilterPredicates(), $predicates);
	}

	
	function getNewActions()
	{
		$actions = array();

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Participant'));
		if ( $method->hasAccess() )
		{
			$method->setRedirectUrl('donothing');
			$actions[] =  array ( 
			        'name' => translate('Добавить участника'),
					'url' => $method->getJSCall(),
					'uid' => 'add-user'
		    );
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
		if ( $method->hasAccess() )
		{
			$actions[] = array();
			$actions[] = array ( 
			        'name' => text(1861),
					'url' => $method->getJSCall(),
					'uid' => 'invite-email'
		    );
		}
		
		return $actions;
	}
	
	function getDeleteActions()
	{
		return array();
	}
}