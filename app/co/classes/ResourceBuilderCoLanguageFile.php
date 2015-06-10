<?php

include_once SERVER_ROOT_PATH."core/classes/ResourceBuilder.php";

class ResourceBuilderCoLanguageFile extends ResourceBuilder
{
    public function build( ResourceRegistry $object )
    {
   		global $text_array;

   		$file_name = strtolower(getSession()->getLanguageUid()).'/resource.php';
   		        
		include SERVER_ROOT_PATH.'/co/bundles/Devprom/ApplicationBundle/Resources/text/'.$file_name;
   		        
   		foreach ( $text_array as $key => $value )
		{
		    $object->addText('co'.$key, $value);
		}
    }
}