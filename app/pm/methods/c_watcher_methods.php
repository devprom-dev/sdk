<?php

 ///////////////////////////////////////////////////////////////////////////////////////
 class WatcherWebMethod extends WebMethod
 {
 	function __construct()
 	{
 		parent::__construct();
 	}

 	function hasAccess()
 	{
 		return getSession()->getParticipantIt()->getId() > 0;
 	}
 	
 	function execute_request()
 	{
 		global $_REQUEST;

	 	if( $_REQUEST['object'] != '' && $_REQUEST['id'] != '' ) 
	 	{
	 		$this->execute($_REQUEST['object'], $_REQUEST['id']);
	 	}
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////////
 class WatchWebMethod extends WatcherWebMethod
 {
 	var $watcher_it, $object_it;
 	
 	function __construct ( $object_it = null )
 	{
 		parent::__construct();

 		$this->setObjectIt($object_it);
 	}
 	
 	function setObjectIt( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
	function getCaption()
	{ 	
		if ( $this->object_it->get('Watchers') != '' )
		{
			return translate('���������� ����������');
		}
		else
		{
			return translate('��������� �� �����������');
		}
	}
	
	function getDescription()
	{
		return text(675);
	}
	
 	function getJSCall()
 	{
 		return parent::getJSCall(
 			array ( 'object' => $this->object_it->object->getClassName(),
 					'id' => $this->object_it->getId() ) 
 			);
 	}
 	
 	function execute( $classname, $objectid )
 	{
 		global $model_factory;
 		
 		$user_it = getSession()->getUserIt();
 		
 		$object = $model_factory->getObject($classname);
 		$object_it = $object->getExact( $objectid ); 

 		if ( $object_it->count() > 0 )
 		{
 			$watcher = $model_factory->getObject2('pm_Watcher', $object_it);
 			$watcher_it = $watcher->getWatched( $user_it );

 			if ( $watcher_it->count() > 0 )
 			{
 				$watcher_it->delete();
 			}
 			else
 			{
 				$watcher->add_parms(
 					array (
	 					'ObjectClass' => $object->getClassName(),
	 					'ObjectId' => $objectid,
	 					'SystemUser' => $user_it->getId()
 						)
 					);
 			}
 		}
 	}
 }

?>