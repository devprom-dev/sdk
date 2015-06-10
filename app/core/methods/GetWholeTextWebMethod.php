<?php

include_once "WebMethod.php";

class GetWholeTextWebMethod extends WebMethod
{
	function __construct( $object_it = null, $attribute = '' )
	{
		parent::__construct();

		$this->object_it = $object_it;
		
		$this->attribute = $attribute;
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
				'attribute' => $this->attribute
		);
		
		foreach( $method_parms as $key => $parm )
		{
			$method_parms[$key] = addslashes(htmlspecialchars($parm, ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		}
		
		foreach( $method_parms as $key => $parm )
		{
			$options[] = $key.": '".$parm."'";
		}
		
		return "javascript: workflowGetField({".join(',',$options)."}, ".$this->getRedirectUrl().")";
	}

	private $object_it = null;
	
	private $attribute = '';
}