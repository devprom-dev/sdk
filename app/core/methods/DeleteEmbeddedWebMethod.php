<?php

include_once "WebMethod.php";

class DeleteEmbeddedWebMethod extends WebMethod
{
 	function execute_request()
 	{
 		 $object = getFactory()->getObject($_REQUEST['class']);

 	 	 if ( $_REQUEST['anchorObject'] > 0 && $_REQUEST['anchorClass'] != '' )
 		 {
 		     $anchor_it = getFactory()->getObject($_REQUEST['anchorClass'])->getExact($_REQUEST['anchorObject']);

 		     $object->setVpdContext($anchor_it);
 		 }
 		 
 		 $object_it = $object->getExact($_REQUEST['object']);
 		 
 		 if ( $object_it->getId() > 0 )
 		 {
	 		 if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) throw new Exception('You have no permissions to delete object');
	 		 
	 		 if ( $object_it->delete() < 1 ) throw new Exception('Object wasn\'t deleted');
 		 } 
	 	
	 	 echo $_REQUEST['callback'].'{"result":"ok"}';
 	}
}