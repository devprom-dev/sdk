<?php

include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class CommentWebMethod extends ObjectCreateNewWebMethod
{
 	private $object_it;
 	
 	function __construct( $object_it = null )
 	{
 		parent::__construct( getFactory()->getObject('Comment') );

 		$this->setAnchorIt($object_it);
 	}

 	function setAnchorIt( $object_it) 
	{
		$this->object_it = $object_it;
	}
	
	function getCaption() 
	{
		return translate('Добавить комментарий');
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Comment'));
	}
	
	function getNewObjectUrl()
	{
		return getSession()->getApplicationUrl($this->object_it).'comments/'.
			strtolower(get_class($this->object_it->object)).'/'.$this->object_it->getId();
	}
}
