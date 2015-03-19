<?php

include_once "classes/ScriptDemoProjectBuilder.php";
include_once "classes/ScriptIntercomBuilder.php";

class DOBAssistPmPlugin extends PluginPmBase
{
 	function getBuilders()
 	{
 	    return array (
 	    		new ScriptDemoProjectBuilder(getSession()),
 	    		new ScriptIntercomBuilder(getSession())
 	    );
 	}
} 