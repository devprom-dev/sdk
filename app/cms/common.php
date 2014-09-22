<?php

 require_once('../common.php');
 require_once('c_view.php');
 
 class CMSModelFactory extends ModelFactory
 {
 	function getEntity()
 	{
 		return new Entity;
 	}
 	
 	function info()
 	{
 	}
 }

 $factory = $model_factory = new CMSModelFactory;
 $model_factory->enableVpd(false);
 
  $factory->sql_log_enabled = true;
