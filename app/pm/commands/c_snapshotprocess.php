<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class SnapshotProcess extends CommandForm
{
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		// proceeds with validation
		$this->checkRequired( array('Caption') );
		
		return true;
 	}
 	
 	function modify( $object_id )
	{
		global $_REQUEST, $model_factory;
		
		$snapshot = $model_factory->getObject('Snapshot');
		$snapshot_it = $snapshot->getExact( $object_id );

		if ( !getFactory()->getAccessPolicy()->can_modify($snapshot_it) ) $this->replyDenied();
		
		$result = $snapshot_it->modify( array( 
				'Caption' => IteratorBase::utf8towin($_REQUEST['Caption']),
				'Description' => IteratorBase::utf8towin($_REQUEST['Description'])
		));

		if ( $result > 0 )
		{
			$this->replyRedirect( $snapshot_it->getViewUrl() );
		}
		else
		{
			$this->replyError( text(1106) );
		}
	}
	
	function create()
	{
		global $model_factory, $_REQUEST;
		
		// proceeds with validation
		$this->checkRequired( array('versionedclass', 'Caption', 'items', 'ListName') );
		
	 	$snapshot = $model_factory->getObject('cms_Snapshot');
			
		$snapshot_id = $snapshot->add_parms(
			array ( 'Caption' => IteratorBase::utf8towin($_REQUEST['Caption']),
					'Description' => IteratorBase::utf8towin($_REQUEST['Description']),
				    'ListName' => IteratorBase::utf8towin($_REQUEST['ListName']),
					'ObjectId' => IteratorBase::utf8towin($_REQUEST['ObjectId']),
					'ObjectClass' => IteratorBase::utf8towin($_REQUEST['ObjectClass']),
				    'SystemUser' => getSession()->getUserIt()->getId() ) 
			);

 		$ids = is_numeric($_REQUEST['items']) 
			? array($_REQUEST['items'])
			: getFactory()->getObject('HashIds')->getIds( $_REQUEST['items'] );
		
 		$versioned = new VersionedObject();
 		
 		$versioned_it = $versioned->getExact($_REQUEST['versionedclass']);
 		
 		if ( $versioned_it->getId() == '' ) $this->replyDenied();
 		
 		$snapshot->freeze( 
 			$snapshot_id, 
 			$versioned_it->getId(),
 			$ids, 
 			$versioned_it->get('Attributes') 
 			);
		
		$this->replyRedirect( $_REQUEST['redirect'].(strpos($_REQUEST['redirect'],'?') === false ? '?baseline=' : '&baseline=').$snapshot_id );
	}
}
