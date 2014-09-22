<?php

namespace Devprom\ProjectBundle\Service\Project;

use Devprom\ProjectBundle\Service\Project\ApplyTemplateService;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ExportService
{
 	function execute( $project_it )
 	{
	 	$text = '<?xml version="1.0" encoding="utf-8"?><entities>';

 		foreach ( ApplyTemplateService::getAllObjects() as $object )
 		{
		    // export records related to the hosted project only
		    $object->addFilter( new \FilterBaseVpdPredicate() );
		    
		    // get all records and serialize it into xml subtree
	 		$text .= $object->serialize2Xml();
 		}
 		
 		$text .= '</entities>';
 		
 		file_put_contents(SERVER_ROOT_PATH.'templates/project/'.$project_it->getId().'.xml', $text);
 	}
 	
 	private function getReferences()
 	{
 		$objects = array();
 		
		$objects[] = getFactory()->getObject('Participant');
		$objects[] = getFactory()->getObject('ParticipantRole');
		$objects[] = getFactory()->getObject('Tag');
		
		$section_it = getFactory()->getObject('ProjectTemplateSections')->getAll();
		
		while( !$section_it->end() )
		{
			$objects = array_merge($objects, $section_it->get('items'));
			
			$section_it->moveNext();
		}

		$classes = array (
				'Request',
				'RequestTag',
				'Task',
				'Milestone',
				'Attachment',
				'Comment',
				'Activity',
				'PMEntityCluster',
				'Snapshot',
				'SnapshotItem',
				'SnapshotItemValue'
		);
		
		foreach( $classes as $class_name )
		{
			$objects[] = getFactory()->getObject($class_name);
		}
		
		return $objects;
 	}
}