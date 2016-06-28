<?php
include_once "classes/notificators/WrtfCKEditorChangeNotificator.php";
include_once "classes/WikiRtfCKEditor.php";
include_once "classes/ScriptWrtfCKEditorBuilder.php";
include_once "classes/StylesheetWrtfCKEditorBuilder.php";
include_once "model/WysiwygMetadataBuilder.php";

class WikiRtfCKEditorPMPlugin extends PluginPMBase
{
    var $enabled;
     
 	function getBuilders()
 	{
 	    return array (
			new WikiRtfCKEditor(),
			new ScriptWrtfCKEditorBuilder(getSession()),
			new StylesheetWrtfCKEditorBuilder(getSession()),
			new WrtfCKEditorChangeNotificator(),
			new WysiwygMetadataBuilder()
 	    );
 	}
 	
    function getModules()
    {
        if ( !$this->checkEnabled() ) return array();
        	
        return array (
                'exportmsword' => 
        			array(
	                    'includes' => array( 'wrtfckeditor/views/WysiwygExportWordPage.php' ),
	                    'classname' => 'WysiwygExportWordPage',
	                    'title' => text('wrtfckeditor2'),
	                    'AccessEntityReferenceName' => 'pm_Project'
	                )
        );
    }
 	
 	function checkEnabled()
 	{
 	    if ( isset($this->enabled) ) return $this->enabled;
 	    
 	    $class_name = strtolower(getSession()->getProjectIt()->get('WikiEditorClass'));
 	    
 	    if ( $class_name == '' ) return false;
 	    
 	    $this->enabled = $class_name == strtolower('WikiRtfCKEditor');
 	    
 	    return $this->enabled;
 	}
}