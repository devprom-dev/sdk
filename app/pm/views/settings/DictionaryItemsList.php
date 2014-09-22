<?php

class DictionaryItemsList extends PMPageList
{
	function getColumns()
	{
		switch ( $this->object->getClassName() )
		{
			case 'pm_ProjectStage':
				$this->object->addAttribute('TaskTypes', '', text(1107), true);
				break;
		}		
		
		return parent::getColumns();
	}

	function IsNeedToDisplayLinks( ) { return false; }
	
	function IsNeedToDisplay( $attr ) 
	{
		global $model_factory;
		
		switch ( $this->object->getClassName() )
		{
			case 'pm_ProjectRole':
			case 'pm_TaskType':
		 		switch( $attr ) 
		 		{
		 			default:
 						return parent::IsNeedToDisplay( $attr );
		 		}
		 		break;
 				
			case 'pm_CustomAttribute':
		 		switch( $attr ) 
		 		{
		 			case 'DefaultValue':
		 			case 'IsVisible':
		 			case 'IsRequired':
		 			case 'IsUnique':
		 				return false;
		 				
		 			default:
 						return parent::IsNeedToDisplay( $attr );
		 		}
		 		break;
		 		
			default:
 				return parent::IsNeedToDisplay( $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory;
		
		$session = getSession();
		
		switch ( $attr )
		{
			case 'TaskTypes':
				$task_type = $model_factory->getObject('TaskType');
				$task_type->addFilter( new TaskTypeStageRelatedPredicate($object_it->getId()) );
				
				$it = $task_type->getAll();
				while ( !$it->end() )
				{
					echo '<div class="line">';
						echo $it->getDisplayName();
					echo '</div>';
					
					$it->moveNext();
				}
				break;
			
			case 'Transitions':
				$transition_it = $object_it->getTransitionIt();
				
				while ( !$transition_it->end() )
				{
					$action = getFactory()->getAccessPolicy()->can_modify($transition_it) ? 'show' : 'view';
					
					echo '<div class="line">';
						echo $transition_it->getFullName();
						echo ' <a href="'.$session->getApplicationUrl().'project/dicts?entity=Transition&pm_TransitionId='.$transition_it->getId().'&pm_Transitionaction='.$action.'">';
							echo '<img src="/images/pencil.png" style="margin-bottom:-3px;">';
						echo '</a>';
					echo '</div>';
					
					$transition_it->moveNext();
				}
				
				break;
				
			case 'EntityReferenceName':
				echo $object_it->getEntityDisplayName(); 
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}		
	}
	
	function getGroupDefault()
	{
	    return 'none';
	}
}
