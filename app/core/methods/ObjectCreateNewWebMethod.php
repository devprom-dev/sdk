<?php

include_once "WebMethod.php";

class ObjectCreateNewWebMethod extends WebMethod
{
	private $object;
	
	function __construct( $object = null )
	{
		parent::__construct();

		$this->object = $object;
		
		$this->setRedirectUrl( 'function() { window.location.reload(); }' );
	}
	
	public function & getObject()
	{
		return $this->object;
	}
	
	function getNewObjectUrl()
	{
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
		
		if ( count($parms) < 1 )
		{
			$parms_string = '[]';
		}
		else
		{
			$strings = array();
			
			foreach( $parms as $key => $value )
			{
				if ( strpos($value, '()') !== false )
					$strings[] = $key.":".addslashes($value);
				else
					$strings[] = $key.":'".addslashes($value)."'";
			}
			
			$parms_string = '{'.join(',',$strings).'}';
		}
		
		foreach( $method_parms as $key => $parm )
		{
			$method_parms[$key] = addslashes(htmlspecialchars($parm, ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		}
		
		return "javascript: workflowNewObject('".join("','", $method_parms)."', ".$parms_string.",".$this->getRedirectUrl().")";
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_create($this->object);
	}
}