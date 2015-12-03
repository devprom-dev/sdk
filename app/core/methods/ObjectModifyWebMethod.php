<?php

include_once "WebMethod.php";

class ObjectModifyWebMethod extends WebMethod
{
	private $object_it;
	private $object_url = '';	
	private static $uid_service = null;
	
	function __construct( $object_it = null )
	{
		parent::__construct();

		if ( !is_object(self::$uid_service) ) self::$uid_service = new ObjectUID();
		 
		$this->object_it = $object_it;
		$this->setObjectUrl($this->object_it->getEditUrl());
		
		$this->setRedirectUrl( 'function() { window.location.reload(); }' );
	}
	
	public function & getObject()
	{
		return $this->object_it->object;
	}
	
	public function setObjectUrl( $url )
	{
		$this->object_url = $url;
	}
	
	public function getObjectUrl()
	{
		return $this->object_url;
	}
	
	function getJSCall( $parms = array() )
	{
		$method_parms = array_merge(
			array (
				'form_url' => $this->getObjectUrl(),
				'class_name' => get_class($this->getObject()),
				'entity_ref' => $this->getObject()->getEntityRefName(),
				'object_id' => $this->object_it->getId(),
				'form_title' => $this->getObject()->getDisplayName(),
				'can_delete' => var_export(getFactory()->getAccessPolicy()->can_delete($this->object_it), true),
				'delete_reason' => getFactory()->getAccessPolicy()->getReason()
			),$parms);
		
		return "javascript: workflowModify(".str_replace('"',"'",json_encode($method_parms, JSON_HEX_APOS)).", ".$this->getRedirectUrl().")";
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_modify($this->object_it);
	}
}