<?php

include_once "WebMethod.php";

class ObjectCreateNewWebMethod extends WebMethod
{
	private $object;
	private $vpd = '';

	function __construct( $object = null )
	{
		parent::__construct();

		$this->object = $object;
		if ( is_object($this->object) ) {
			$this->setVpd($this->object->getVpdValue());
		}

		$this->setRedirectUrl( 'function() { window.location.reload(); }' );
	}

	public function getObject()
	{
		return $this->object;
	}

	public function getCaption()
	{
		return $this->object->getDisplayName();
	}

	public function setVpd( $vpd ) {
		$this->vpd = $vpd;
	}

	function getNewObjectUrl()
	{
		if ( $this->vpd != '' ) $this->object->setVpdContext($this->vpd);
		return $this->object->getPageName();
	}
	
	function getJSCall( $parms = array(), $title = '' )
	{
		$method_parms = array (
				$this->getNewObjectUrl(),
				get_class($this->object),
				$this->object->getEntityRefName(),
				$title != '' ? $title : $this->object->getDisplayName()
		);
		
		foreach( $method_parms as $key => $parm )
		{
			$method_parms[$key] = addslashes(htmlspecialchars($parm, ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		}
		
		return "javascript: workflowNewObject('".join("','", $method_parms)."', ".str_replace('"',"'",json_encode($parms, JSON_HEX_APOS)).",".$this->getRedirectUrl().")";
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_create($this->object);
	}
}