<?php
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class CommentWebMethod extends ObjectCreateNewWebMethod
{
 	private $object_it;
 	
 	function __construct( $object_it = null ) {
 		parent::__construct( getFactory()->getObject('Comment') );
 		$this->doSelectProject(false);
 		$this->setAnchorIt($object_it);
 	}

 	function setAnchorIt( $object_it)  {
		$this->object_it = $object_it;
	}
	
	function getCaption() {
		return text(2477);
	}
	
	function hasAccess() {
 	    if ( is_object($this->object_it) ) {
            if ( !getFactory()->getAccessPolicy()->can_modify_attribute($this->object_it->object, 'RecentComment') )
                return false;
        }
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}
	
	function getNewObjectUrl()
	{
		return getSession()->getApplicationUrl($this->object_it).'comments/'.
			strtolower(get_class($this->object_it->object)).'/'.$this->object_it->getId();
	}
}
