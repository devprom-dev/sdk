<?php

include_once SERVER_ROOT_PATH."core/classes/ResourceBuilder.php";

class ResourceBuilderLanguageFiles extends ResourceBuilder
{
    public function build( ResourceRegistry $object )
    {
   		global $text_array;

   		$file_name = strtolower(getSession()->getLanguage()->getLanguage()).'/resource.php';
   		        
		include SERVER_ROOT_PATH.'/lang/'.$file_name;
   		        
   		foreach ( $text_array as $key => $value )
		{
		    $object->addText($key, $value);
		}
    }
}