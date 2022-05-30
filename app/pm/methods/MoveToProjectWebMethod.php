<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class MoveToProjectWebMethod extends WebMethod
{
 	var $request_it;
 	var $url = '?';
 	
 	function __construct( $request_it = null )
 	{
 		parent::__construct();
 		
 		$this->setRequestIt($request_it);
 		$this->setRedirectUrl("devpromOpts.updateUI");
 	}
 	
 	function setRequestIt($request_it) {
 		$this->request_it = $request_it;
 	}

 	function setUrl( $url ) {
 	    $this->url = $url;
    }
 	
	function getCaption() 
	{
		return text(866);
	}

 	function getMethodName()
	{
		return 'AttributeProject';
	}
	
 	function getJSCall( $parms = array() )
	{
 		return "javascript:processBulk('".$this->getCaption()."','".$this->url."formonly=true&operation=".$this->getMethodName()."&Project=".$parms['Project']."', ".$this->request_it->getId().", ".$this->getRedirectUrl().")";
	}
		
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_modify($this->request_it->object)
            && getFactory()->getAccessPolicy()->can_modify_attribute($this->request_it->object, 'Project');
	}
}
