<?php

class SBAssistPMPlugin extends PluginPMBase
{
 	function getBuilders()
 	{
 	    return array (
            new ScriptCrispBuilder(getSession())
 	    );
 	}
}