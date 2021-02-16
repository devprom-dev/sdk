<?php

include_once "SelectWebMethod.php";

class SelectRefreshWebMethod extends SelectWebMethod
{
 	function SelectRefreshWebMethod()
 	{
 		$this->setType( '' );
 		parent::SelectWebMethod();
 	}
 	
 	function getName()
 	{
 		return '';
 	}
 	
 	function setType( $type )
 	{
 		$this->type = $type;
 	}
 	
 	function getType()
 	{
 		return $this->type;
 	}
 	
 	function getClass()
 	{
 		return '';
 	}
}