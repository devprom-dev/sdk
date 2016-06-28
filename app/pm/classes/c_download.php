<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class DownloadActionIterator extends OrderedIterator
 {
 	function getObjectIt()
 	{
		return getFactory()->getObject($this->get('EntityRefName'))->getExact($this->get('ObjectId')); 
 	}
 	
 	function getUserIt()
 	{
 		$sql = " SELECT u.*, MAX(act.RecordCreated) DownloadDate " .
 				"  FROM cms_User u, pm_DownloadActor act " .
 				" WHERE u.cms_UserId = act.SystemUser " .
 				"   AND act.DownloadAction = ".$this->getId().
 				"   AND u.Caption <> 'anonymous' ".
 				" GROUP BY u.cms_UserId" .
 				" ORDER BY DownloadDate DESC ";
 		
 		return getFactory()->getObject('cms_User')->createSQLIterator($sql);
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class DownloadAction extends Metaobject
 {
 	function DownloadAction() 
 	{
 		parent::Metaobject('pm_DownloadAction');
		$this->defaultsort = 'RecordCreated DESC';
 	}
 	
 	function createIterator() {
 		return new DownloadActionIterator( $this );
 	}
 	
	function getPage() {
		return 'artefacts.php?mode=downloads&';
	}
	
	function process( $objectid, $entityrefname )
	{
		global $model_factory;

		$user_id = getSession()->getUserIt()->getId();

		if ( $user_id < 1 )
		{
			$user = $model_factory->getObject('cms_User');
			$user_it = $user->getByRef('Login', 'anonymous');
			$user_id = $user_it->getId();
		}

		if ( $user_id < 1 ) return;

		
		$it = $this->getByRefArray(
			array( 'ObjectId' => $objectid, 'EntityRefName' => $entityrefname )
			);
			
		if ( $it->count() < 1 )
		{
			$action_id = $this->add_parms(
				array( 'Caption' => $objectid, 'ObjectId' => $objectid, 'EntityRefName' => $entityrefname ));
		}
		else
		{
			$action_id = $it->getId();
		}
		
		getFactory()->getObject('pm_DownloadActor')->add_parms(
			array('SystemUser' => $user_id, 'DownloadAction' => $action_id ) );
	}
	
	function getDownloads( $object_it )
	{
		return $this->getDownloadsBase ( $object_it->getId(), 
			$object_it->object->getClassName() );
	}

	function getDownloadsBase( $object_id, $entity_ref )
	{
		$sql = 'SELECT (SELECT COUNT(1) FROM pm_DownloadActor a ' .
			   '		 WHERE a.DownloadAction = m.pm_DownloadActionId) Downloads ' .
			   '  FROM pm_DownloadAction m' .
			   ' WHERE m.ObjectId = '.$object_id.
			   "   AND m.EntityRefName = '".$entity_ref."' ".
			   ' ORDER BY RecordCreated DESC';
			   
		$it = $this->createSqlIterator($sql);
		return $it->get('Downloads') == '' ? 0 : $it->get('Downloads');
	}
 }

?>
