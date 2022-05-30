<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WikiPageUsedByModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof WikiPage ) return;
        $object->addPersister(new \WikiPageUsedByPersister());
    }
}