<?php

include_once SERVER_ROOT_PATH."core/classes/ResourceBuilder.php";

class ResourceBuilderPluginsLanguageFiles extends ResourceBuilder
{
    public function build( ResourceRegistry $object )
    {
        global $plugins;

        if ( !is_object($plugins) ) return;
        
        $text_array = $plugins->initializeResources(getSession()->getLanguageUid());

       	foreach ( $text_array as $key => $value )
		{
		    $object->addText($key, $value);
		}
    }
}