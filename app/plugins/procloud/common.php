<?php

 include '../../common.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";
include_once SERVER_ROOT_PATH."pm/classes/sessions/PMSession.php";

 $plugins = new PluginsFactory();
 
 $model_factory = new ModelFactoryExtended($plugins, getCacheService());
 
 getLanguage();
 
 // create session object 
 if ( $_REQUEST['project'] != '' )
 {
	$session = new PMSession( $_REQUEST['project'] );
 }
 else
 {
     $session = new SessionBase;
 }

  $model_factory->setAccessPolicy( new AccessPolicy($model_factory->getCacheService()) );
 
 $user_it = getSession()->getUserIt();