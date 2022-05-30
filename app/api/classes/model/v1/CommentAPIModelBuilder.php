<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class CommentAPIModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Comment ) return;
        $object->resetAttributeGroup('ObjectClass', 'system');
        $object->resetAttributeGroup('ObjectId', 'system');
        $object->resetAttributeGroup('PrevComment', 'system');
   	}
}