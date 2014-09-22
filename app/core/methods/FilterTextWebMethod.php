<?php

include_once "WebMethod.php";

class FilterTextWebMethod extends WebMethod
{
 	var $method_name, $title, $parm, $style, $script;
 	
 	function FilterTextWebMethod( $title = '', $parm = 'search' )
 	{
 		$this->method_name = md5($this->getMethodName());
 		$this->title = $title;
 		$this->setValueParm($parm);
 		$this->style = 'width:230px;';

 		$this->script = "javascript: filterLocation.setup('".$this->getValueParm()."='+$(this).val())";
 		
 		parent::WebMethod();
 	}

	function getCaption()
	{
		return $this->title;
	}
	
 	function hasAccess()
 	{
 		return true;
 	}

	function setStyle( $style )
	{
		$this->style = $style;
	}
	
 	function getStyle()
 	{
 		return $this->style;
 	}
 	
 	function setScript( $script )
 	{
 	    $this->script = $script;
 	}

 	function drawSelect( $parms_array = array() ) 
 	{
		echo '<input type="text" valueparm="'.$this->getValueParm().'" class="btn-small input-large" placeholder="'.$this->title.'" style="'.$this->getStyle().'" value="'.$this->getValue().'" onchange="'.$this->script.'">';
 	}
 	
 	function execute_request()
 	{
 		global $_REQUEST;
 		$this->execute($_REQUEST['setting'], $_REQUEST['value']);
 		
 		if ( $this->getValueParm() != '' )
 		{
 			echo $this->getValueParm().'='.trim($_REQUEST['value']);
 		}
 	}

 	function execute ( $method, $value )
 	{
 	}
}