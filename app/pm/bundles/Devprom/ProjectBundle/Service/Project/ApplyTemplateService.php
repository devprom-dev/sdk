<?php

namespace Devprom\ProjectBundle\Service\Project;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ApplyTemplateService
{
	private $reset_state = true;

	public function setResetState( $flag )
	{
		$this->reset_state = $flag;
	}
	
 	function apply( $template_it, $project_it, $sections = null )
 	{
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \PMChangeLogNotificator() );
		getFactory()->getEventsManager()->removeNotificator( new \PMEmailNotificator() );
		
 		// store initial values for iteration dates
 		$context = new \CloneContext();
 		
 		$context->setResetState($this->reset_state);
 		
 		$template_path = $template_it->object->getTemplatePath($template_it->get('FileName') );

 		$file = fopen ( $template_path, 'r' );
 		$xml = fread( $file, filesize($template_path) );

 		$state_objects = array();
 		
 		$objects = is_array($sections) ? $this->getSectionObjects($sections) : $this->getAllObjects();
 				
		foreach ( $objects as $object )
		{
			$object->addFilter( new \FilterBaseVpdPredicate() );
			
			switch ( $object->getEntityRefName() )
			{
				case 'cms_Resource':
				case 'pm_Transition':
				case 'pm_TransitionAttribute':
				case 'pm_TransitionResetField':
				case 'pm_TransitionRole':
				case 'pm_TransitionPredicate':
				case 'pm_TaskTypeStage':
				case 'pm_Predicate':
				case 'pm_StateAction':
				case 'pm_StateAttribute':
				case 'pm_Workspace':
				case 'pm_WorkspaceMenu':
				case 'pm_WorkspaceMenuItem':
				case 'pm_AccessRight':
					
					$object->deleteAll();
						
					break;
					
				case 'pm_State':
					
					$state_objects[] = $object;
					
					break;
			}

			\CloneLogic::Run( $context, $object, $object->createXMLIterator($xml), $project_it ); 
		}
		
		// remove unnecessary data
		foreach( $state_objects as $state )
		{
			$state_it = $state->getRegistry()->Query(
					array (
							new \FilterBaseVpdPredicate(),
							new \StateHasNoTransitionsPredicate(),
							new \StateHasNoObjectsPredicate()
					)
			);
			
			while( !$state_it->end() )
			{
				$state_it->delete();
				$state_it->moveNext();
			}
		}
 		
 		getSession()->truncate();
 	}
 	
 	private function getSectionObjects( $sections )
 	{
 		$objects = array();
 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();
		
 		while ( !$section_it->end() )
 		{
 			if ( !in_array($section_it->get('ReferenceName'), $sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
 			$objects = array_merge($objects, $section_it->get('items'));
 			
 			$section_it->moveNext();
 		}
 		
 		return $objects;
 	}
 	
 	static public function getAllObjects()
 	{
 		$objects = array (
 				getFactory()->getObject('Participant'),
 				getFactory()->getObject('ParticipantRole'),
 				getFactory()->getObject('Tag')
 		);
 		 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();

 	 	while ( !$section_it->end() )
 		{
 			$objects = array_merge($objects, $section_it->get('items'));
 			
 			$section_it->moveNext();
 		}

 		return $objects;
 	}
}