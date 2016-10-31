<?php
include_once SERVER_ROOT_PATH.'core/c_session.php';
include_once 'COAccessPolicy.php';
include_once 'COSystemTriggers.php';
include_once "ResourceBuilderCoLanguageFile.php";
include_once "ProjectWelcomeStylesheetBuilder.php";

class COSession extends SessionBase
{
 	function getSite() {
 	    return 'co';
 	}

 	function getApplicationUrl() {
 	    return '/co/';
 	}
 	
 	function createBuilders()
 	{
 	    return array_merge(
 	    		array (
 	    			new ResourceBuilderCoLanguageFile()
 	    		),
 	    		parent::createBuilders(),
 	    		array (
 	    			new ProjectWelcomeStylesheetBuilder(getSession()),
                    new COSystemTriggers()
 	    		)
 	    );
 	}
}
