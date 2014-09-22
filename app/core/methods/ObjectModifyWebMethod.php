<?php

include_once "WebMethod.php";

class ObjectModifyWebMethod extends WebMethod
{
	private $object_it;
	
	private static $uid_service = null;
	
	function __construct( $object_it = null )
	{
		parent::__construct();

		if ( !is_object(self::$uid_service) ) self::$uid_service = new ObjectUID();
		 
		$this->object_it = $object_it;
		
		$this->setRedirectUrl( 'function() { window.location.reload(); }' );
	}
	
	public function & getObject()
	{
		return $this->object_it->object;
	}
	
	function getJSCall( $parms = array() )
	{
		$method_parms = array (
				'form_url' => $this->object_it->getEditUrl(),
				'class_name' => get_class($this->getObject()),
				'entity_ref' => $this->getObject()->getEntityRefName(),
				'object_id' => $this->object_it->getId(),
				'form_title' => substr(self::$uid_service->getUidTitle($this->object_it), 0, 120),
				'can_delete' => var_export(getFactory()->getAccessPolicy()->can_delete($this->object_it), true),
				'delete_reason' => getFactory()->getAccessPolicy()->getReason()
		);
		
		foreach( $method_parms as $key => $parm )
		{
			$method_parms[$key] = addslashes(htmlspecialchars($parm, ENT_COMPAT | ENT_HTML401, 'windows-1251'));
		}
		
		foreach( $method_parms as $key => $parm )
		{
			$options[] = $key.": '".$parm."'";
		}
		
		return "javascript: workflowModify({".join(',',$options)."}, ".$this->getRedirectUrl().")";
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_modify($this->object_it);
	}
}