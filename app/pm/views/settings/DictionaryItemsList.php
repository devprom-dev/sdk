<?php

class DictionaryItemsList extends PMPageList
{
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
