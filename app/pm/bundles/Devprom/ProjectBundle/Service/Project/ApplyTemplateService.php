<?php

namespace Devprom\ProjectBundle\Service\Project;
use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ApplyTemplateService
{
	private $reset_state = true;

	public function setResetState( $flag )
	{
		$this->reset_state = $flag;
	}
	
 	function apply( $template_it, $project_it, $sections = array(), $except_sections = array() )
 	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
 		// store initial values for iteration dates
 		$context = new \CloneContext();
 		
 		$context->setResetState($this->reset_state);
 		
 		$template_path = $template_it->object->getTemplatePath($template_it->get('FileName') );

 		$file = fopen ( $template_path, 'r' );
 		$xml = fread( $file, filesize($template_path) );

 		$state_objects = array();
 		
 		$objects = count($sections) > 0 
 				? $this->getSectionObjects($sections, $except_sections) 
 				: $this->getAllObjects($except_sections);
 				
		foreach ( $objects as $object )
		{
			$object->resetFilters();
			
			$object->addFilter( new \FilterBaseVpdPredicate() );
			
			$iterator = $object->createXMLIterator($xml);
			
			switch ( $object->getEntityRefName() )
			{
				case 'cms_Resource':
				case 'cms_Snapshot':
				case 'pm_Transition':
				case 'pm_TransitionAttribute':
				case 'pm_TransitionResetField':
				case 'pm_TransitionRole':
				case 'pm_TransitionPredicate':
				case 'pm_TaskTypeStage':
				case 'pm_Predicate':
				case 'pm_StateAction':
				case 'pm_StateAttribute':
				case 'pm_AccessRight':
				case 'pm_Workspace':
					
					$object->deleteAll();
						
					break;
					
				case 'pm_CustomReport':

					if ( $iterator->count() > 0 )
					{
						// remove common (glogal) reports
						$report_it = $object->getByRefArray(
								array (
										'Author' => -1
								)
						);
						
						while( !$report_it->end() )
						{
							$report_it->delete();
							$report_it->moveNext();
						}
					}
					
					break;

				case 'pm_UserSetting':

					if ( $iterator->count() > 0 )
					{
						// remove common (glogal) settings for reports/modules
						$it = $object->getByRefArray(
								array (
										'Participant' => -1
								)
						);
						
						while( !$it->end() )
						{
							$it->delete();
							$it->moveNext();
						}
					}
					
					break;
					
				case 'pm_State':
					
					$state_objects[] = $object;
					
					break;
			}

			\CloneLogic::Run( $context, $object, $iterator, $project_it ); 
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
		
		$metrics_service = new StoreMetricsService();
		$metrics_service->execute($project_it);
 		
 		getSession()->truncate();
 	}
 	
 	static protected function getSectionObjects( $sections, $except_sections = array() )
 	{
 		$objects = array(
 				getFactory()->getObject('ProjectRole')
 		);
 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();
		
 		while ( !$section_it->end() )
 		{
 			if ( in_array($section_it->get('ReferenceName'), $except_sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
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
 	
 	static public function getAllObjects( $except_sections = array() )
 	{
 		$objects = array();
 		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();

 	 	while ( !$section_it->end() )
 		{
 		 	if ( in_array($section_it->get('ReferenceName'), $except_sections) )
 			{
 				$section_it->moveNext();
 				continue;
 			}
 			
 			$objects = array_merge($objects, $section_it->get('items'));

 			if ( $section_it->get('ReferenceName') == 'pm_Project')
 			{
 				$objects = array_merge($objects, array (
 						getFactory()->getObject('Participant'),
		 				getFactory()->getObject('ParticipantRole'),
			 			getFactory()->getObject('Release'),
			 			getFactory()->getObject('Iteration')
				));
 			}
 			
 			$section_it->moveNext();
 		}

 		$result = array();
 		
 		foreach( $objects as $object )
 		{
 			$hash = get_class($object).$object->getEntityRefName();
 			
 			if ( !array_key_exists($hash, $result) ) $result[$hash] = $object;
 		}
 		
 		return $result;
 	}
}