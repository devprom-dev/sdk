<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "parsers/CustomDictionaryParser.php";

class CustomAttributesObjectReferencesModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( !$object->attributesHasOrigin(ORIGIN_CUSTOM) ) return;
		$object->addReferenceParser( new CustomDictionaryParser() );
    }
}