<?php

class WatchWebMethod extends WebMethod
{
 	var $watcher_it, $object_it;
 	
 	function __construct ( $object_it = null )
 	{
 		parent::__construct();
 		$this->setObjectIt($object_it);
 	}

 	function getModule()
    {
        if ( is_object($this->object_it) ) {
            return getSession()->getApplicationUrl($this->object_it).'methods.php';
        }
        return parent::getModule();
    }

 	function setObjectIt( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
	function getCaption()
	{ 	
		if ( $this->object_it->get('Watchers') != '' )
		{
			return translate('Прекратить наблюдение');
		}
		else
		{
			return translate('Наблюдать за изменениями');
		}
	}
	
	function getDescription()
	{
		return text(675);
	}
	
    function getJSCall($parms = array())
 	{
 		return parent::getJSCall(
 			array ( 'classname' => get_class($this->object_it->object),
 					'objectid' => $this->object_it->getId() )
 			);
 	}

     function execute_request()
     {
         if( $_REQUEST['classname'] != '' && $_REQUEST['objectid'] != '' ) {
             $this->execute($_REQUEST['classname'], $_REQUEST['objectid']);
         }
     }

     function execute( $classname, $objectid )
 	{
 		$user_it = getSession()->getUserIt();
 		$object_it = getFactory()->getObject($classname)->getRegistry()->Query(
            array(
                new FilterVpdPredicate(),
                new FilterInPredicate(TextUtils::parseIds($objectid))
            )
        );

 		if ( $object_it->count() > 0 )
 		{
 			$watcher = getFactory()->getObject2('pm_Watcher', $object_it);
 			$watcher_it = $watcher->getWatched( $user_it );

 			if ( $watcher_it->count() > 0 ) {
 				$watcher_it->delete();
 			}
 			else {
 				$watcher->add_parms( array (
                    'ObjectClass' => strtolower(get_class($object_it->object)),
                    'ObjectId' => $objectid,
                    'SystemUser' => $user_it->getId()
                ));
 			}
 		}
 	}

     function hasAccess() {
         return getSession()->getParticipantIt()->getId() > 0;
     }
}