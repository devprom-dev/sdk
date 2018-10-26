<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class IssueAutoActionMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof IssueAutoAction ) return;

        $metadata->setAttributeVisible('OrderNum', true);
        $metadata->setAttributeVisible('Conditions', true);
        $metadata->setAttributeVisible('Actions', true);
        $metadata->setAttributeVisible('NewComment', true);
        $metadata->setAttributeType('EventType', 'REF_AutoActionEventId');
        $metadata->addAttributeGroup('NewComment', 'comment');

        foreach( array('ClassName', 'ReferenceName') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }

    }
}