<?php

include_once "WebMethod.php";

class AutoSaveTextWebMethod extends WebMethod
{
 	var $rows, $type;
 	
 	function AutoSaveTextWebMethod()
 	{
 		parent::WebMethod();
 		
 		$this->rows = 4;
 		$this->type = 'textarea';
 	}
 	
 	function setRows( $rows )
 	{
 		$this->rows = $rows;
 	}
 	
 	function setInput()
 	{
 		$this->type = 'input';
 	}
 	
 	function getTitle()
 	{
 		return '';
 	}
 	
 	function execute_request() 
 	{
 		global $_REQUEST;
 		$this->execute($_REQUEST, IteratorBase::Utf8ToWin($_REQUEST['value']));
 	}
 	
 	function draw( $parms, $default_value = '' ) 
 	{
 		global $script_number;
 		$script_number += 1;

		if ( count($parms) < 1 )
		{
			array_push($parms, 'dummy');
		}

		$title = $this->getTitle();

		if ( !$this->hasAccess() )
		{
	 		echo '<div>';
	 			echo $default_value; 	
	 		echo '</div>';
		}
		else
		{
 			switch ( $this->type )
 			{
 				case 'input':
 					echo '<input type="text" class="autosave-input input-block-level" data-href="'.$this->getModule().'?method='.get_class($this).'" data-save="'.htmlentities(json_encode($parms)).'" style="text-align:right;" title="'.$title.'" value="'.$default_value.'">';
 					break;
 					
 				case 'textarea':
 					echo '<textarea class="autosave-input input-block-level" title="'.$title.'" data-href="'.$this->getModule().'?method='.get_class($this).'" data-save="'.htmlentities(json_encode($parms)).'" rows='.$this->rows.' >'.$default_value.'</textarea>';
 					break;
 			}
		}
 	}
}
