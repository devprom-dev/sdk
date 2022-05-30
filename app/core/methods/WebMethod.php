<?php

class WebMethod
{
 	var $title, $module;
 	
 	private $redirect_url = '';
 	private $freeze_method = null;
 	private $filter_name = '';
    private $async = false;
    private $beforeCallback = 'donothing';
 	
 	function WebMethod() 
 	{
 	}

 	function exportHeaders()
 	{
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset='.APP_ENCODING);
 	}
 	
 	function execute_request() {
 		// place the code to execute method as a
 		// result of the page calling 
 	}

	function hasAccess() {
		// ensures a user has access to execute the method
		return true;
	}
	
  	function getModule()
 	{
 		if ( $this->module != '' ) return $this->module;
 		
 		// returns the name of a php module
 		// where execute_request method is implemented
 		return getSession()->getApplicationUrl().'methods.php';
 	}

 	function setModule( $module )
 	{
 		$this->module = $module;
 	}
 	
 	function getCaption() {
 		// returns the caption of a link
 		return $this->title;
 	}

	function setCaption ( $title )
	{
		$this->title = $title;
	}

	function setAsync( $value ) {
        $this->async = $value;
    }

    function setBeforeCallback( $value ) {
        $this->beforeCallback = $value;
    }

 	function getPreCaption() {
 		// returns the text is displayed before caption is
 		return '';
 	}

	function getDescription() {
		// returns description of a link (as title is displayed under the pointer)
		return '';
	}

 	function getWarning() {
 		// returns the warning message when user clicks the link
 		return '';
 	}
 	
 	// returns the url where the browser is needed to be redirected after the method execution
	function getRedirectUrl()
	{
		return $this->redirect_url;		
	}
	
	// set the url where the browser is needed to be redirected after the method execution
	function setRedirectUrl( $url )
	{
	    $this->redirect_url = $url;
	}
 	
	function setFilter( $filter )
	{
		$this->filter_name = $filter;
	}
	
	function getFilter()
	{
		return $this->filter_name;
	}
	
	function getFreezeMethod()
	{
		if ( is_object($this->freeze_method) ) return $this->freeze_method;
		 
		$this->freeze_method = new FilterFreezeWebMethod();
		
	 	$this->freeze_method->setFilter( $this->filter_name );
		
		return $this->freeze_method;
	}
	
	function setFreezeMethod( $method )
	{
		$this->freeze_method = $method;
	}
	
	function getValueParm()
	{
		return $this->parm;
	}
	
	function setValueParm( $parm )
	{
		$this->parm = $parm;
	}
	
	function getPersistedValue()
	{
		$default = $this->getFreezeMethod();
		if ( is_object($default) && $this->filter_name != '' )
		{
			return $default->getValue( $this->getValueParm() );
		}
		return null;
	}
	
 	function getValue()
 	{
 		$persisted = $this->getPersistedValue();
		return !is_null($persisted) ? $persisted : '';
 	}

	function getUrl( $parms_array )
	{
 		$module_name = $this->getModule();
 		
 		$query_items = array();
 		array_push($query_items, 'method='.get_class($this));
 		
 		$parms_keys = array_keys($parms_array);
 		for($i = 0; $i < count($parms_keys); $i++) {
 			array_push($query_items, $parms_keys[$i].'='.$parms_array[$parms_keys[$i]]);
 		}
		$query_string = '?'.join('&', $query_items);

		return $module_name.$query_string;
	}
	
	function getJSCall( $parms = array() )
	{
		$keys = array_keys($parms);
		$data = array();
		
		foreach ( $keys as $key )
		{
			$value = preg_match('/function\s*\(/', $parms[$key]) ? $parms[$key] : "'".$parms[$key]."'";
			
			array_push( $data, "'".$key."' : ".$value );	
		}
		
		$redirect = $this->getRedirectUrl();
		if ( !preg_match('/function\s*\(/', $redirect) ) $redirect = "'".$redirect."'";
		
		return "javascript: runMethod('".$this->getModule().'?method='.get_class($this).
			"', {".join(',', $data)."}, ".$redirect.", '".$this->getWarning()."', ".($this->async?'true':'false').", ".$this->beforeCallback.");";
	}
 	
 	function getParametersUrl( $parms_array ) {
 		return '';
 	}

 	function getMethodName()
 	{
 		return 'method_'.get_class($this);
 	}

	function wintoutf8($s)
 	{
 		return IteratorBase::wintoutf8($s);
 	}

 	function getObjectIt()
 	{
 		if ( $_REQUEST['class'] == '' ) throw new Exception('Class name is required');
 		
 		if ( $_REQUEST['object'] == '' ) throw new Exception('Object is required');
 		
 		$class_name = getFactory()->getClass($_REQUEST['class']);
 		
 		if ( !class_exists($class_name) ) throw new Exception('Unknown class name: '.$class_name);
 		
 		$object_it = getFactory()->getObject($class_name)->getExact($_REQUEST['object']);
 		
 		if ( $object_it->getId() < 1 ) throw new Exception('Unknown object identifier: '.$_REQUEST['object']);

 		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) throw new Exception('You have no permissions to modify the object');
 		
 		return $object_it;
 	}

 	function parseFilterValue($value, $context) {
 	    return $value;
    }

    function redirect( $url ) {
        echo JsonWrapper::encode(
            array (
                'message' => 'location',
                'state' => 'redirect',
                'object' => $url,
            )
        );
    }
}
