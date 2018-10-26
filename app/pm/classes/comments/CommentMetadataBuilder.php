<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/CommentAuthorPersister.php";

class CommentMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Comment) return;

        $metadata->addAttributeGroup('ObjectClass', 'system');
        $metadata->addAttributeGroup('ObjectId', 'system');
        $metadata->addAttributeGroup('PrevComment', 'system');
        $metadata->setAttributeType('Caption', 'WYSIWYG');
        $metadata->addPersister( new CommentAuthorPersister() );
    }
}