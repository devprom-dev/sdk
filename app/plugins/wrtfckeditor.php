<?php

include "wrtfckeditor/WikiRtfCKEditorPMPlugin.php";

class WikiRTFCKEditorPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'wrtfckeditor';
 	}
 
  	function getFileName()
 	{
 		return basename(__FILE__);
 	}
 	
 	function getCaption()
 	{
 		return text('wrtfckeditor1');
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new WikiRtfCKEditorPMPlugin );
 	}
}
