<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

abstract class EntityModifyProjectTrigger extends SystemTriggersBase
{
	abstract protected function checkEntity( $object_it );
	
	abstract protected function & getObjectReferences( & $object_it );

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_MODIFY ) return;
	    if ( !$this->checkEntity($object_it) ) return;
	    
	    $references = $this->getObjectReferences($object_it);
	    if ( !is_array($references) ) return;
	    
	    if ( !array_key_exists('Project', $content) )
	    {
		    $data = $this->getRecordData();
		    
		    if ( !array_key_exists('Project', $data) ) return;
		    if ( $data['Project'] == $object_it->get('Project') ) return;
		    
		    $project_it = getFactory()->getObject('Project')->getExact($data['Project']);
	    }
	    else
	    {
	    	$project_it = $object_it->getRef('Project');
	    }

	    $this->moveEntity( $object_it, $project_it, $references );
	}
	
	protected function moveEntity( & $object_it, & $target_it, & $references )
	{
 	    global $model_factory, $session;
 	    
 	    $xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
 	    
 	    foreach( $references as $object )
 	    {
 	       $xml .= $object->serialize2Xml();
 	    }
 	    
 	    $xml .= '</entities>';

 	    $this->updateChangeLog( $object_it, $target_it );
 	    
 	    // preserve issue key to be created with the same Id
 	    $context = new CloneContext();
 	    
 	    $ids_map = array();
 	    
 	    foreach( $object_it->idsToArray() as $object_id )
 	    {
 	    	$ids_map[$object_it->object->getEntityRefName()][$object_id] = $object_id; 
 	    }
 	    
 	    // remove source object 
 	    $this->deleteObsolete($object_it);
 	    
 	    $project_it = getSession()->getProjectIt();
 
 	    // duplicate serialized data in the target project
 	    $session = new PMSession( $target_it, getSession()->getAuthenticationFactory() );
 	    
 	    // bind data to existing objects if any
 	    $context->setUseExistingReferences( true );
		$context->setResetState(false);
 	    $context->setResetDates(false);
 	    $context->setIdsMap($ids_map);
 	    
 	 	foreach( $references as $object )
 	    {
 	    	$object = getFactory()->getObject(get_class($object));

     	    CloneLogic::Run( $context, $object, $object->createXMLIterator($xml), $target_it);
 	    }
 	    
 	    // restore the current session
 	    $session = new PMSession( $project_it, getSession()->getAuthenticationFactory() );
	}

	protected function deleteObsolete( & $object_it )
	{
		$object_it->delete();

		$watcher = getFactory()->getObject2('pm_Watcher', $object_it);
		$watcher_it = $watcher->getAll();
		
		while( !$watcher_it->end() )
		{
			$watcher_it->object->delete($watcher_it->getId());
			$watcher_it->moveNext();
		}
	}
	
	protected function updateChangeLog( $object_it, $target_it )
	{
	    global $model_factory;

	    $project_it = getSession()->getProjectIt();
	    
		// store message the issue has been moved
		$message = str_replace( '%1', $project_it->getDisplayName(), 
			str_replace('%2', $target_it->getDisplayName(), text(1122)) );  
		
		$change_parms = array(
			'Caption' => $object_it->getDisplayName(),
			'ObjectId' => $object_it->getId(),
			'EntityName' => $object_it->object->getDisplayName(),
			'ClassName' => strtolower(get_class($object_it->object)),
			'ChangeKind' => 'deleted',
			'Content' => $message,
			'VisibilityLevel' => 1,
			'SystemUser' => getSession()->getUserIt()->getId()
		);

		$change = getFactory()->getObject('ObjectChangeLog');
		$change->disableVpd();
		$change->add_parms( $change_parms );

		// move related changes into target project
		$change_it = $change->getRegistry()->Query(
			array (
				new ChangeLogItemFilter($object_it),
				new FilterAttributePredicate('ChangeKind', 'added,modified,commented' )
			)
		);
		DAL::Instance()->Query(" UPDATE ObjectChangeLog SET VPD = '".$target_it->get('VPD')."' WHERE ObjectChangeLogId IN (".join(',',$change_it->idsToArray()).")");

		$change_parms['ChangeKind'] = 'added';
		$change_parms['VPD'] = $target_it->get('VPD');
		$change->add_parms( $change_parms );
	}
}
 