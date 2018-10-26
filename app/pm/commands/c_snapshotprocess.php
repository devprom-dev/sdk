<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class SnapshotProcess extends CommandForm
{
 	function validate()
 	{
		// proceeds with validation
		$this->checkRequired( array('Caption') );
		
		return true;
 	}
 	
 	function modify( $object_id )
	{
		$snapshot = getFactory()->getObject('Snapshot');
		$snapshot_it = $snapshot->getExact( $object_id );

		if ( !getFactory()->getAccessPolicy()->can_modify($snapshot_it) ) $this->replyDenied();
		
		$result = $snapshot->modify_parms($snapshot_it->getId(), array( 
				'Caption' => IteratorBase::utf8towin($_REQUEST['Caption']),
				'Description' => IteratorBase::utf8towin($_REQUEST['Description'])
		));

		if ( $result > 0 ) {
			$this->replyRedirect( $snapshot_it->getViewUrl() );
		}
		else {
			$this->replyError( text(1106) );
		}
	}
	
	function create()
	{
		// proceeds with validation
		$this->checkRequired( array('versionedclass', 'Caption', 'items', 'ListName') );
		
	 	$snapshot = getFactory()->getObject('cms_Snapshot');
			
		$snapshot_id = $snapshot->add_parms(
			array ( 'Caption' => IteratorBase::utf8towin($_REQUEST['Caption']),
					'Description' => IteratorBase::utf8towin($_REQUEST['Description']),
				    'ListName' => IteratorBase::utf8towin($_REQUEST['ListName']),
					'ObjectId' => IteratorBase::utf8towin($_REQUEST['ObjectId']),
					'ObjectClass' => IteratorBase::utf8towin($_REQUEST['ObjectClass']),
				    'SystemUser' => getSession()->getUserIt()->getId() ) 
			);

 		$versioned = new VersionedObject();
 		
 		$versioned_it = $versioned->getExact($_REQUEST['versionedclass']);
 		if ( $versioned_it->getId() == '' ) $this->replyDenied();
 		
 		$snapshot->freeze( 
 			$snapshot_id, 
 			$versioned_it->getId(),
            \TextUtils::parseIds($_REQUEST['items']),
 			$versioned_it->get('Attributes') 
 			);
		
		$this->replyRedirect( $_REQUEST['redirect'].(strpos($_REQUEST['redirect'],'?') === false ? '?baseline=' : '&baseline=').$snapshot_id );
	}
}
