<?php

include "classes/ScriptDemoProjectBuilder.php";

class SaasAssistPmPlugin extends PluginPmBase
{
 	function getBuilders()
 	{
 	    return array (
 	    		new ScriptDemoProjectBuilder(getSession())
 	    );
 	}
} 