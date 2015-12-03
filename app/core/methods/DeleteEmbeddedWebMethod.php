<?php

include_once "WebMethod.php";

class DeleteEmbeddedWebMethod extends WebMethod
{
 	function execute_request()
 	{
		$class_name = getFactory()->getClass($_REQUEST['class']);
		if ( $class_name == '' ) return;
 		$object = getFactory()->getObject($class_name);

 	 	if ( $_REQUEST['anchorObject'] > 0 && $_REQUEST['anchorClass'] != '' )
 		{
			$class_name = getFactory()->getClass($_REQUEST['anchorClass']);
			if ( $class_name != '' ) {
				$object->setVpdContext(getFactory()->getObject($class_name)->getExact($_REQUEST['anchorObject']));
			}
 		}
 		 
 		$object_it = $object->getExact($_REQUEST['object']);
 		if ( $object_it->getId() > 0 )
 		{
	 		if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) {
				throw new Exception('You have no permissions to delete object');
			}
	 		if ( $object_it->delete() < 1 ) {
				throw new Exception('Object wasn\'t deleted');
			}
 		}
	 	
	 	echo $_REQUEST['callback'].'{"result":"ok"}';
 	}
}