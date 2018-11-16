<?php
define( 'CODE_ISOLATE', '/<code([^>]*)>([\S\s]+)<\/code>/i' );
define( 'CODE_RESTORE', '/<code([^>]*)>([0-9]+)<\/code>/i' );
define( 'REGEX_COMMENTS', '/<span\s+comment-id="(\d+)"\s*>([^<]+)<\/span>/i' );

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
            'searchartifacts' =>
                array (
                    'includes' => array( 'wrtfckeditor/views/SearchArtifactsPage.php' ),
                    'classname' => 'SearchArtifactsPage',
                    'title' => text('wrtfckeditor7'),
                    'AccessEntityReferenceName' => 'pm_ChangeRequest'
                ),
            'searchtexttemplate' =>
                array (
                    'includes' => array( 'wrtfckeditor/views/SearchTextTemplatePage.php' ),
                    'classname' => 'SearchTextTemplatePage',
                    'title' => text('wrtfckeditor8'),
                    'AccessEntityReferenceName' => 'pm_ChangeRequest'
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