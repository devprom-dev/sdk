<?php
include "classes/notificators/WrtfCKEditorChangeNotificator.php";
include "classes/WikiRtfCKEditor.php";
include "classes/ScriptWrtfCKEditorBuilder.php";
include "classes/StylesheetWrtfCKEditorBuilder.php";
include "model/WysiwygMetadataBuilder.php";
include "classes/WikiConverterBuilderWYSIWYG.php";
include "classes/WikiImporterBuilderPanDoc.php";

class WikiRtfCKEditorPMPlugin extends PluginPMBase
{
    var $enabled;

 	function getBuilders()
 	{
 	    $builders = array (
            new WikiRtfCKEditor(),
            new ScriptWrtfCKEditorBuilder(getSession()),
            new StylesheetWrtfCKEditorBuilder(getSession()),
            new WrtfCKEditorChangeNotificator(),
            new WysiwygMetadataBuilder()
        );

        if ( !$this->checkEnabled() ) return $builders;

 	    return array_merge($builders,
            array (
                new WikiConverterBuilderWYSIWYG(),
                new WikiImporterBuilderPanDoc()
            )
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