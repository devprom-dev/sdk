<?php

include_once SERVER_ROOT_PATH."core/classes/ResourceBuilder.php";

class ResourceBuilderPluginsLanguageFiles extends ResourceBuilder
{
    public function build( ResourceRegistry $object )
    {
        $text_array = \PluginsFactory::Instance()->initializeResources($object->getObject()->getLanguageUid());
       	foreach ( $text_array as $key => $value ) {
		    $object->addText($key, $value);
		}
    }
}